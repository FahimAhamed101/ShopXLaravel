<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FlashSaleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:Ecommerce Management')];
    }

    public function index(): View
    {
        $flashSale = FlashSale::query()->first();
        $products = Product::query()
            ->with('primaryImage')
            ->whereIn('id', $flashSale?->products ?? [])
            ->get();

        return view('admin.flash-sale.index', compact('flashSale', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sale_start' => ['required', 'date'],
            'sale_end' => ['required', 'date', 'after_or_equal:sale_start'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['integer', 'distinct', 'exists:products,id'],
            'status' => ['nullable', 'boolean'],
        ]);

        FlashSale::query()->updateOrCreate(
            ['id' => FlashSale::query()->value('id') ?? 1],
            [
                'sale_start' => $validated['sale_start'],
                'sale_end' => $validated['sale_end'],
                'products' => array_values($validated['products']),
                'is_active' => $request->boolean('status'),
            ],
        );

        AlertService::updated('Flash sale updated successfully.');

        return to_route('admin.flash-sales.index');
    }

    public function getProducts(Request $request): JsonResponse
    {
        $page = max($request->integer('page', 1), 1);
        $products = Product::query()
            ->with('primaryImage')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $query->where('name', 'like', '%'.$request->string('q')->trim().'%');
            })
            ->orderBy('name')
            ->paginate(20, ['*'], 'page', $page);

        return response()->json([
            'results' => $products->getCollection()->map(fn (Product $product) => [
                'id' => $product->id,
                'text' => $product->name,
                'image' => imageUrl($product->primaryImage?->path),
            ])->values(),
            'pagination' => ['more' => $products->hasMorePages()],
        ]);
    }
}
