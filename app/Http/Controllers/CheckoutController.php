<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Mail\OrderReceived;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;
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
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Корзина пуста.'], 422);
            }
            return redirect()->route('cart.index')->with('error', 'Корзина пуста.');
        }

        $validated = $request->validated();
        $discountAmount = isset($cart['discount']) ? (float) $cart['discount'] : 0;
        $promoCodeId = ($cart['promo'] ?? null) && $discountAmount > 0 ? $cart['promo']->id : null;

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
            try {
                $order->load('items.product', 'items.productVariant', 'promoCode');
                Mail::to($adminEmail)->send(new OrderReceived($order));
            } catch (\Throwable $e) {
                Log::warning('Checkout: не удалось отправить письмо администратору', [
                    'order_id' => $order->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        CartController::clearCart();

        $paymentService = PaymentService::fromConfig();
        $usePayment = $paymentService->isTestMode()
            || config('payment.tbank.terminal_key')
            || config('payment.tbank.use_demo_terminal');
        $redirectUrl = route('order.confirmation', $order);
        if ($usePayment) {
            $order->load('items');
            $payment = $paymentService->createPaymentForOrder($order);
            if ($payment->pay_url) {
                $redirectUrl = $payment->pay_url;
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['redirect' => $redirectUrl]);
        }
        if ($redirectUrl !== route('order.confirmation', $order)) {
            return redirect()->away($redirectUrl);
        }
        return redirect()->route('order.confirmation', $order)->with('success', 'Заявка успешно отправлена.');
    }

    public function confirmation(Order $order)
    {
        $order->load('items.product', 'items.productVariant', 'promoCode', 'payment');
        return view('order.confirmation', compact('order'));
    }
}
