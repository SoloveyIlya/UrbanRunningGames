<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Mail\OrderReceived;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function show()
    {
        $cart = CartController::getCartForDisplay();
        if (empty($cart['items'])) {
            return redirect()->route('cart.index')->with('error', 'Корзина пуста. Добавьте товары перед оформлением.');
        }
        return view('checkout.form', $cart);
    }

    public function store(CheckoutRequest $request)
    {
        $cart = CartController::getCartForDisplay();
        if (empty($cart['items'])) {
            return redirect()->route('cart.index')->with('error', 'Корзина пуста.');
        }

        $validated = $request->validated();
        $discountAmount = isset($cart['discount']) ? (float) $cart['discount'] : 0;
        $promoCodeId = $cart['promo']?->id ?? null;

        $order = Order::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'new',
            'promo_code_id' => $promoCodeId,
            'discount_amount' => $discountAmount > 0 ? $discountAmount : null,
        ]);

        foreach ($cart['items'] as $row) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $row['product']->id,
                'product_variant_id' => $row['variant']?->id,
                'quantity' => $row['quantity'],
                'price_amount' => $row['price'],
            ]);
        }

        if ($promoCodeId) {
            \App\Models\PromoCode::where('id', $promoCodeId)->increment('times_used');
        }

        $adminEmail = config('mail.admin');
        if ($adminEmail) {
            $order->load('items.product', 'items.productVariant', 'promoCode');
            Mail::to($adminEmail)->send(new OrderReceived($order));
        }

        CartController::clearCart();

        return redirect()->route('order.confirmation', $order)->with('success', 'Заявка успешно отправлена.');
    }

    public function confirmation(Order $order)
    {
        $order->load('items.product', 'items.productVariant', 'promoCode');
        return view('order.confirmation', compact('order'));
    }
}
