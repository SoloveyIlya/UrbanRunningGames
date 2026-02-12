<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderExportController extends Controller
{
    /**
     * Экспорт заявок в CSV (доступно только авторизованным в админке).
     */
    public function csv(Request $request): StreamedResponse
    {
        $query = Order::query()
            ->with(['items.product', 'items.productVariant', 'promoCode'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $filename = 'orders_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel

            fputcsv($handle, [
                'ID', 'Дата', 'Имя', 'Телефон', 'Email', 'Статус', 'Промокод', 'Скидка (₽)',
                'Позиции (товар, вариант, кол-во, цена)', 'Сумма до скидки', 'Итого',
            ], ';');

            foreach ($query->cursor() as $order) {
                $itemsText = $order->items->map(function ($item) {
                    $name = $item->product->name ?? '—';
                    $variant = $item->variant_label ?? '—';
                    return "{$name} ({$variant}): {$item->quantity} × {$item->price_amount}";
                })->implode(' | ');
                $subtotal = $order->getSubtotalAmountAttribute();
                $total = $subtotal - (float) ($order->discount_amount ?? 0);

                fputcsv($handle, [
                    $order->id,
                    $order->created_at->format('d.m.Y H:i'),
                    $order->name,
                    $order->phone,
                    $order->email,
                    Order::statusOptions()[$order->status] ?? $order->status,
                    $order->promoCode?->code ?? '—',
                    $order->discount_amount ?? '0',
                    $itemsText,
                    number_format($subtotal, 2, '.', ''),
                    number_format($total, 2, '.', ''),
                ], ';');
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
