@extends('layoutsnew.app')

@push('styles')
<link href="{{asset('backend/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="{{asset('backend/libs/sweetalert2/sweetalert2.min.js')}}"></script>
<script src="{{asset('backend/js/pages/sweet-alerts.init.js')}}"></script>
@endpush

@section('content')
<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Adminox</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('roles.title')}}</a></li>
                            <li class="breadcrumb-item active">All</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('roles.title')}}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-3">{{ __('roles.all') }}</h4>
                        <a href="{{ route('roles.create') }}"
                            class="btn btn-primary btn-sm">
                            + {{__('global.new')}}
                        </a>
                    </div>
                    <x-alert />

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <th>Name</th>
                                        <th>Permissions Count</th>
                                        <th>Description</th>
                                        <th>Guard Name</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            <tbody>
                                @if($roles->isNotEmpty())
                                @foreach($roles as $role)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$role->name}}</td>
                                    <td>{{$role->permissions_count}}</td>
                                    <td>{{$role->description}}</td>
                                    <td>{{$role->guard_name}}</td>
                                    <td>{{\Carbon\Carbon::parse($role->created_at)->format('d M, Y')}}</td>
                                    <td>
                                        <div class="button-list d-flex align-items-center gap-2">
                                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-info">{{ __('global.update') }}</a>
                                            <a href="javascript:void(0)" data-action="{{ route('roles.destroy', $role) }}" class="btn btn-sm btn-danger btn-delete" data-id="{{$role->id}}">{{ __('global.delete') }}</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                        {{$roles->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('backend/js/ajax-form-submit.js') }}"></script>
@endpush