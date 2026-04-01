<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSection;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductSectionController extends Controller implements HasMiddleware
{
    static function Middleware(): array
    {
        return [
            new Middleware('permission:Section Management')
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $sectionCategory = ProductSection::first();
        return view('admin.product-section.index', compact('sectionCategory'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'category_one' => ['nullable', 'integer', 'exists:categories,id'],
            'category_two' => ['nullable', 'integer', 'exists:categories,id'],
            'category_three' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        ProductSection::updateOrCreate(
            ['id' => 1],
            [
                'category_one' => $request->category_one,
                'category_two' => $request->category_two,
                'category_three' => $request->category_three
            ]
        );

        AlertService::updated();

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
