<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $cart = CartController::getCartForDisplay();
        if (empty($cart['items'])) {
            return redirect()->route('cart.index')->with('error', 'Корзина пуста.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email',
            'comment' => 'nullable|string|max:2000',
        ], [
            'name.required' => 'Укажите имя.',
            'phone.required' => 'Укажите телефон.',
            'email.required' => 'Укажите email.',
            'email.email' => 'Некорректный email.',
        ]);

        $order = Order::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'new',
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

        CartController::clearCart();

        return redirect()->route('order.confirmation', $order)->with('success', 'Заявка успешно отправлена.');
    }

    public function confirmation(Order $order)
    {
        $order->load('items.product', 'items.productVariant');
        return view('order.confirmation', compact('order'));
    }
}
