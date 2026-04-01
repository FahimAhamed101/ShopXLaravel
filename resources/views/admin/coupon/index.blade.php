@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Coupons</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">Create Coupon</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Code</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Used</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $coupon->code }}</td>
                                    <td class="text-secondary">{{ $coupon->value }}</td>
                                    <td class="text-info">{{ $coupon->is_percent ? '%' : 'Fixed' }}</td>
                                    <td class="text-secondary">{{ $coupon->used }}</td>
                                    <td class="text-secondary">{{ $coupon->start_date }}</td>
                                    <td class="text-secondary">{{ $coupon->end_date }}</td>
                                    @if ($coupon->is_active)
                                        <td class="text-secondary"><span class="badge bg-success-lt">Active</span></td>
                                    @else
                                        <td class="text-secondary"><span class="badge bg-danger-lt">Inactive</span></td>
                                    @endif
                                    <td>
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}"><i class="ti ti-edit"></i></a>
                                        <a class="text-danger delete-item"
                                            href="{{ route('admin.coupons.destroy', $coupon) }}"><i
                                                class="ti ti-trash"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No Coupons</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $coupons->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
