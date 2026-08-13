@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Coupon</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.coupons.store') }}" method="POST" class="coupon-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Code</label>
                                <input type="text" class="form-control" name="code" value="{{ old('code') }}">
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Value</label>
                                <input type="number" min="0.01" step="0.01" class="form-control" name="value" value="{{ old('value') }}">
                                <x-input-error :messages="$errors->get('value')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Is Percentage</label>
                                <select name="is_percent" id="" class="form-control">
                                    <option @selected(old('is_percent') == '0') value="0">No</option>
                                    <option @selected(old('is_percent') == '1') value="1">Yes</option>
                                </select>
                                <x-input-error :messages="$errors->get('is_percent')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Minimum Spend</label>
                                <input type="number" min="0" step="0.01" class="form-control" name="minimum_spend"
                                    value="{{ old('minimum_spend', 0) }}">
                                <x-input-error :messages="$errors->get('minimum_spend')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Maximum Spend</label>
                                <input type="number" min="0" step="0.01" class="form-control" name="maximum_spend"
                                    value="{{ old('maximum_spend') }}">
                                <x-input-error :messages="$errors->get('maximum_spend')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Usage Limit Per Coupon</label>
                                <input type="number" min="1" class="form-control" name="usage_limit_per_coupon"
                                    value="{{ old('usage_limit_per_coupon', 1) }}">
                                <x-input-error :messages="$errors->get('usage_limit_per_coupon')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Usage Limit Per Customer</label>
                                <input type="number" min="1" class="form-control" name="usage_limit_per_customer"
                                    value="{{ old('usage_limit_per_customer', 1) }}">
                                <x-input-error :messages="$errors->get('usage_limit_per_customer')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Start Date</label>
                                <input type="text" class="form-control datepicker" name="start_date" placeholder=""
                                    value="{{ old('start_date') }}">
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">End Date</label>
                                <input type="text" class="form-control datepicker" name="end_date" placeholder=""
                                    value="{{ old('end_date') }}">
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-2">
                                <label class="form-check form-switch form-switch-3">
                                    <input class="form-check-input" type="checkbox" checked="" name="is_active"
                                        id="status" value="1">
                                    <span class="form-check-label">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary mt-3" onclick="$('.coupon-form').submit()">Create</button>
            </div>
        </div>
    </div>
    </div>
@endsection
