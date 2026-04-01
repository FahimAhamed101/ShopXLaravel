@extends('vendor-dashboard.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Withdraw Methods</h3>
                <div class="card-actions">
                    <a href="{{ route('vendor.withdraw-methods.create') }}" class="btn btn-primary">Add Method</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawMethods as $method)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $method->withdrawMethod->name }}</td>
                                    <td>{{ date('Y-m-d', strtotime($method->created_at)) }}</td>
                                    <td>
                                        <a href="{{ route('vendor.withdraw-methods.edit', $method) }}"
                                            class="text-primary"><i class="ti ti-edit"></i></a>
                                        <a class="text-danger delete-item"
                                            href="{{ route('vendor.withdraw-methods.destroy', $method) }}"><i
                                                class="ti ti-trash"></i></a>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No Methods</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{-- {{ $orders->links() }} --}}
                </div>
            </div>
        </div>
    </div>
@endsection
