<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    private const SESSION_KEY = 'cart';

    private static function cartKey(?int $productId, ?int $variantId): string
    {
        return 'p' . ($productId ?? 0) . '_v' . ($variantId ?? 0);
    }

    public static function getItems(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public static function getCount(): int
    {
        $items = self::getItems();
        return array_sum(array_column($items, 'quantity'));
    }

    public static function clearCart(): void
    {
        Session::put(self::SESSION_KEY, []);
    }

    /** @return array{items: array, total: float} */
    public static function getCartForDisplay(): array
    {
        $raw = self::getItems();
        if (empty($raw)) {
            return ['items' => [], 'total' => 0];
        }
        $productIds = array_unique(array_column($raw, 'product_id'));
        $variantIds = array_filter(array_unique(array_column($raw, 'variant_id')));
        $products = Product::with(['coverMedia', 'media', 'variants'])
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');
        $variants = $variantIds
            ? ProductVariant::with('product')->whereIn('id', $variantIds)->get()->keyBy('id')
            : collect();

        $items = [];
        $total = 0.0;
        foreach ($raw as $key => $row) {
            $product = $products->get($row['product_id'] ?? 0);
            if (! $product) {
                continue;
            }
            $variant = isset($row['variant_id']) && $row['variant_id']
                ? $variants->get($row['variant_id'])
                : null;
            $quantity = (int) ($row['quantity'] ?? 1);
            $price = $variant
                ? (float) ($variant->price_override ?? $product->price_amount)
                : (float) $product->price_amount;
            $subtotal = $price * $quantity;
            $total += $subtotal;
            $items[] = [
                'key' => $key,
                'product' => $product,
                'variant' => $variant,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
            ];
        }
        return ['items' => $items, 'total' => $total];
    }

    public function index()
    {
        $cart = self::getCartForDisplay();
        return view('cart.index', $cart);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1|max:99',
        ]);
        $productId = (int) $request->product_id;
        $variantId = $request->variant_id ? (int) $request->variant_id : null;
        $quantity = (int) ($request->quantity ?: 1);

        $product = Product::active()->findOrFail($productId);
        if ($variantId) {
            $variant = $product->adminVariants()->findOrFail($variantId);
            if (! $variant->is_active) {
                return back()->with('error', 'Выбранный вариант недоступен.');
            }
        } else {
            if ($product->variants()->exists()) {
                return back()->with('error', 'Выберите вариант (размер/цвет).');
            }
        }

        $key = self::cartKey($productId, $variantId);
        $cart = self::getItems();
        $cart[$key] = [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => ($cart[$key]['quantity'] ?? 0) + $quantity,
        ];
        Session::put(self::SESSION_KEY, $cart);

        return redirect()->route('cart.index')->with('success', 'Товар добавлен в корзину.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'quantity' => 'required|integer|min:0|max:99',
        ]);
        $key = $request->key;
        $quantity = (int) $request->quantity;
        $cart = self::getItems();
        if (! isset($cart[$key])) {
            return back()->with('error', 'Позиция не найдена в корзине.');
        }
        if ($quantity === 0) {
            unset($cart[$key]);
        } else {
            $cart[$key]['quantity'] = $quantity;
        }
        Session::put(self::SESSION_KEY, $cart);
        return redirect()->route('cart.index')->with('success', 'Корзина обновлена.');
    }

    public function remove(Request $request, string $key)
    {
        $cart = self::getItems();
        unset($cart[$key]);
        Session::put(self::SESSION_KEY, $cart);
        return redirect()->route('cart.index')->with('success', 'Позиция удалена из корзины.');
    }
}
