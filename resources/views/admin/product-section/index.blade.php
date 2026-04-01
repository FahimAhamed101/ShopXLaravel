@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Product Section</h3>
                <div class="card-actions">
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.product-sections.store') }}" method="POST" class="coupon-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Section One Category</label>
                                <select name="category_one" id="" class="form-control select2   ">
                                    <option value="">No Category</option>
                                    @foreach (getNestedCategories() as $category)
                                        <option @selected($category->id == $sectionCategory?->category_one) value="{{ $category->id }}">{{ $category->name }}</option>
                                        @if (count($category->children_nested) > 0)
                                            @foreach ($category->children_nested as $child)
                                                <option @selected($child->id == $sectionCategory?->category_one) value="{{ $child->id }}">- {{ $child->name }}</option>
                                                @if (count($child->children_nested) > 0)
                                                    @foreach ($child->children_nested as $subchild)
                                                        <option @selected($subchild->id == $sectionCategory?->category_one) value="{{ $subchild->id }}">-- {{ $subchild->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('sale_start')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Section Two Category</label>
                                <select name="category_two" id="" class="form-control select2">
                                    <option value="">No Category</option>
                                    @foreach (getNestedCategories() as $category)
                                        <option @selected($category->id == $sectionCategory?->category_two) value="{{ $category->id }}">{{ $category->name }}</option>
                                        @if (count($category->children_nested) > 0)
                                            @foreach ($category->children_nested as $child)
                                                <option @selected($child->id == $sectionCategory?->category_two) value="{{ $child->id }}">- {{ $child->name }}</option>
                                                @if (count($child->children_nested) > 0)
                                                    @foreach ($child->children_nested as $subchild)
                                                        <option @selected($subchild->id == $sectionCategory?->category_two) value="{{ $subchild->id }}">-- {{ $subchild->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('sale_two')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Section Three Category</label>
                                <select name="category_three" id="" class="form-control select2">
                                    <option value="">No Category</option>
                                    @foreach (getNestedCategories() as $category)
                                        <option @selected($category->id == $sectionCategory?->category_three) value="{{ $category->id }}">{{ $category->name }}</option>
                                        @if (count($category->children_nested) > 0)
                                            @foreach ($category->children_nested as $child)
                                                <option @selected($child->id == $sectionCategory?->category_three) value="{{ $child->id }}">- {{ $child->name }}</option>
                                                @if (count($child->children_nested) > 0)
                                                    @foreach ($child->children_nested as $subchild)
                                                        <option @selected($subchild->id == $sectionCategory?->category_three) value="{{ $subchild->id }}">-- {{ $subchild->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_three')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary mt-3" onclick="$('.coupon-form').submit()">Update</button>
            </div>
        </div>
    </div>
    </div>
@endsection
