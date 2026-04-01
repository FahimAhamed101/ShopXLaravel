@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Popular Category</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.popular-categories.store') }}" method="POST" class="coupon-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label required">Categories</label>
                                <select name="categories[]" id="" class="form-control select2" multiple>
                                    @foreach (getNestedCategories() as $category)
                                        <option @selected(in_array($category->id, $categories)) value="{{ $category->id }}">{{ $category->name }}</option>
                                        @if(count($category->children_nested) > 0)
                                            @foreach ($category->children_nested as $child)
                                                <option @selected(in_array($child->id, $categories)) value="{{ $child->id }}">- {{ $child->name }}</option>
                                                @if(count($child->children_nested) > 0)
                                                    @foreach ($child->children_nested as $subchild)
                                                        <option @selected(in_array($subchild->id, $categories)) value="{{ $subchild->id }}">-- {{ $subchild->name }}</option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                <span class="text-muted">The selected categories products will be shown on top products section in home page</span>
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
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
