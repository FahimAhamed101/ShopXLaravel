@extends('vendor-dashboard.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Withdraw Method</h3>
                <div class="card-actions">
                    <a href="{{ route('vendor.withdraw-methods.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('vendor.withdraw-methods.update', $withdraw_method) }}" method="POST" class="withdraw-method-form">
                    @csrf
                    @method('PUT')
                    <div class="row">

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label required">Gateway</label>
                                <select name="gateway" class="form-control" id="gateway">
                                    <option value="">Select Gateway</option>
                                    @foreach ($withdrawMethods as $method)
                                        <option @selected($method->id == $withdraw_method->withdraw_method_id) value="{{ $method->id }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('gateway')" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            @foreach ($withdrawMethods as $method)
                                <div class="alert alert-info method-{{ $method->id }}" style="display: none">
                                    <div class="gateway-details">
                                        {!! $method->instruction !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label required">Description</label>
                                <textarea name="description" id="editor">{{ $withdraw_method->description }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                    </div>


                </form>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary mt-3" onclick="$('.withdraw-method-form').submit()">Update</button>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#gateway').on('change', function() {
                var methodId = $(this).val();
                $('.alert-info').hide();
                $('.method-' + methodId).show();
            })
        })
    </script>
@endpush
