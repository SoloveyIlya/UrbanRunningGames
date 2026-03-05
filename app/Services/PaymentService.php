<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        protected bool $testMode,
        protected string $terminalKey,
        protected string $password,
        protected string $apiUrl,
        protected ?string $notificationUrl = null,
        protected ?string $successUrl = null,
        protected ?string $failUrl = null
    ) {}

    public static function fromConfig(): self
    {
        $config = config('payment.tbank');
        return new self(
            testMode: config('payment.test_mode', true),
            terminalKey: $config['terminal_key'] ?? '',
            password: $config['password'] ?? '',
            apiUrl: $config['api_url'] ?? 'https://securepay.tinkoff.ru/v2/Init',
            notificationUrl: $config['notification_url'] ?? null,
            successUrl: $config['success_url'] ?? null,
            failUrl: $config['fail_url'] ?? null
        );
    }

    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    /**
     * Создать платёж для заказа и вернуть URL для перехода на оплату.
     * В тестовом режиме возвращает ссылку на страницу имитации успешной оплаты.
     */
    public function createPaymentForOrder(Order $order): Payment
    {
        $totalKopecks = (int) round(
            ($order->getSubtotalAmountAttribute() - (float) ($order->discount_amount ?? 0)) * 100
        );
        $totalKopecks = max(1000, $totalKopecks); // минимум 10 руб для СБП

        if ($this->testMode || $this->terminalKey === '' || $this->password === '') {
            return $this->createTestPayment($order, $totalKopecks);
        }

        return $this->createTbankPayment($order, $totalKopecks);
    }

    protected function createTestPayment(Order $order, int $totalKopecks): Payment
    {
        $payment = Payment::create([
            'provider' => 'tbank',
            'external_payment_id' => 'test-' . $order->id . '-' . Str::random(8),
            'amount' => $totalKopecks / 100,
            'currency' => 'RUB',
            'status' => Payment::STATUS_PENDING,
            'pay_url' => route('payment.test-pay', ['order' => $order->id]),
            'payload' => ['test' => true],
        ]);

        $order->update(['payment_id' => $payment->id]);

        return $payment;
    }

    protected function createTbankPayment(Order $order, int $totalKopecks): Payment
    {
        $orderId = 'order-' . $order->id . '-' . now()->format('YmdHis');
        $description = 'Заказ #' . $order->id . ' Urban Running Games';
        $params = [
            'TerminalKey' => $this->terminalKey,
            'Amount' => $totalKopecks,
            'OrderId' => $orderId,
            'Description' => Str::limit($description, 250),
            'Language' => 'ru',
        ];
        if ($this->notificationUrl) {
            $params['NotificationURL'] = $this->notificationUrl;
        }
        if ($this->successUrl) {
            $params['SuccessURL'] = $this->successUrl;
        } else {
            $params['SuccessURL'] = \Illuminate\Support\Facades\URL::to(route('order.confirmation', $order));
        }
        if ($this->failUrl) {
            $params['FailURL'] = $this->failUrl;
        }
        $params['Token'] = $this->buildToken($params);

        $response = Http::asJson()->post($this->apiUrl, $params);
        $body = $response->json();

        if (! $response->successful() || empty($body['PaymentId']) || empty($body['Status'])) {
            throw new \RuntimeException(
                $body['Message'] ?? 'Ошибка создания платежа: ' . $response->body()
            );
        }

        $payment = Payment::create([
            'provider' => 'tbank',
            'external_payment_id' => (string) $body['PaymentId'],
            'amount' => $totalKopecks / 100,
            'currency' => 'RUB',
            'status' => $this->mapTbankStatus($body['Status']),
            'pay_url' => $body['PaymentURL'] ?? null,
            'payload' => $body,
        ]);

        $order->update(['payment_id' => $payment->id]);

        return $payment;
    }

    /**
     * Подпись запроса T-Bank: значения параметров по алфавиту ключей + Password, SHA-256.
     */
    protected function buildToken(array $params): string
    {
        unset($params['Token'], $params['Receipt'], $params['DATA']);
        $params['Password'] = $this->password;
        ksort($params);
        $concatenated = implode('', array_map('strval', $params));
        return hash('sha256', $concatenated);
    }

    protected function mapTbankStatus(string $status): string
    {
        return match (strtolower($status)) {
            'authorized', 'confirmed' => Payment::STATUS_PAID,
            'rejected', 'refunded', 'canceled', 'deadline_expired' => Payment::STATUS_FAILED,
            default => Payment::STATUS_PENDING,
        };
    }

    /**
     * Проверка подписи уведомления от T-Bank и обработка статуса.
     */
    public function verifyNotificationToken(array $data): bool
    {
        $token = $data['Token'] ?? '';
        unset($data['Token']);
        $data['Password'] = $this->password;
        ksort($data);
        $expected = hash('sha256', implode('', array_map('strval', $data)));
        return hash_equals($expected, $token);
    }
}
