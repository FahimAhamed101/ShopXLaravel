@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Our Features</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.our-features.create') }}" class="btn btn-primary">Create Feature</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th class="w-100px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($features as $feature)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <img style="width: 50px" src="{{ asset($feature->icon) }}" alt="">
                                    </td>
                                    <td>{{ $feature->title }}</td>
                                    <td>
                                        @if($feature->status == 1)
                                        <span class="badge bg-primary-lt">Active</span></td>
                                        @else
                                        <span class="badge bg-danger-lt">Inactive</span></td>
                                        @endif
                                    <td>
                                        <a href="{{ route('admin.our-features.edit', $feature) }}"><i class="ti ti-edit"></i></a>
                                        <a class="text-danger delete-item" href="{{ route('admin.our-features.destroy', $feature) }}"><i class="ti ti-trash"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No Data Available</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $features->links() }}
                  </div>
            </div>
        </div>
    </div>
@endsection
