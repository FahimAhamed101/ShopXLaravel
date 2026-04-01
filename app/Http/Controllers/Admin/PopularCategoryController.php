<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PopularCategory;
use App\Services\AlertService;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PopularCategoryController extends Controller implements HasMiddleware
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
        $categories = PopularCategory::first()?->categories ?? [];

        return view('admin.popular-category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'categories' => 'required'
        ]);

        PopularCategory::updateOrCreate(
            ['id' => 1],
            ['categories' => $request->categories]
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
