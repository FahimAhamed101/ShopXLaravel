@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Social Links</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.social-links.create') }}" class="btn btn-primary">Create Link</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Icon</th>
                                <th>Link</th>
                                <th>Status</th>
                                <th class="w-100px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($socialLinks as $link)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <img style="width: 50px; background-color: rgb(94, 94, 94);"
                                            src="{{ asset($link->icon) }}" alt="">
                                    </td>
                                    <td>{{ $link->url }}</td>
                                    <td>
                                        @if ($link->status == 1)
                                            <span class="badge bg-primary-lt">Active</span>
                                    </td>
                                @else
                                    <span class="badge bg-danger-lt">Inactive</span></td>
                            @endif
                            <td>
                                <a href="{{ route('admin.social-links.edit', $link) }}"><i class="ti ti-edit"></i></a>
                                <a class="text-danger delete-item"
                                    href="{{ route('admin.social-links.destroy', $link) }}"><i class="ti ti-trash"></i></a>
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
                    {{ $socialLinks->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
