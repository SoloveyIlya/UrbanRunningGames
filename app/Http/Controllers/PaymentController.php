<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    /**
     * T-Bank отправляет сюда уведомление о смене статуса платежа (CONFIRMED и т.д.).
     */
    public function webhook(Request $request): Response
    {
        $data = $request->all();
        $paymentService = PaymentService::fromConfig();
        if (! $paymentService->verifyNotificationToken($data)) {
            return response('', 403);
        }

        $orderId = $data['OrderId'] ?? '';
        if (! preg_match('/^order-(\d+)-/', $orderId, $m)) {
            return response('OK', 200);
        }
        $order = Order::find((int) $m[1]);
        if (! $order || ! $order->payment_id) {
            return response('OK', 200);
        }

        $payment = $order->payment;
        if (! $payment) {
            return response('OK', 200);
        }

        $status = strtolower((string) ($data['Status'] ?? ''));
        if (in_array($status, ['confirmed', 'authorized'], true)) {
            $payment->markPaid();
            $order->update(['paid_at' => now()]);
        }
        if (in_array($status, ['rejected', 'canceled', 'refunded', 'deadline_expired'], true)) {
            $payment->update(['status' => \App\Models\Payment::STATUS_FAILED]);
        }

        return response('OK', 200);
    }

    /**
     * Тестовая страница: имитация успешной оплаты (режим PAYMENT_TEST_MODE=true).
     */
    public function testPay(Order $order)
    {
        if (! PaymentService::fromConfig()->isTestMode()) {
            abort(404);
        }
        if ($order->isPaid()) {
            return redirect()->route('order.confirmation', $order)->with('info', 'Заказ уже оплачен.');
        }
        $payment = $order->payment;
        if (! $payment || ! $payment->isPending()) {
            return redirect()->route('order.confirmation', $order)->with('error', 'Платёж не найден или уже обработан.');
        }
        $payment->markPaid();
        $order->update(['paid_at' => now()]);
        return redirect()->route('order.confirmation', $order)->with('success', 'Оплата прошла успешно (тестовый режим).');
    }
}
