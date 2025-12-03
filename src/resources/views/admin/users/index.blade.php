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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('users.title')}}</a></li>
                            <li class="breadcrumb-item active">All</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('users.title')}}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-3">{{ __('users.all') }}</h4>
                        <a href="{{ route('users.create') }}"
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
                                        <th>Role</th>
                                        <th>Permissions Count</th>
                                        <th>Locked</th>
                                        <th>Last Login</th>
                                        <th>Created At</th>
                                        <th>Created By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            <tbody>
                                @if($users->isNotEmpty())
                                @foreach($users as $user)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->roles->pluck('name')->implode(', ')}}</td>
                                    <td>{{$user->permissions->pluck('name')->implode(', ')}}</td>
                                    <td>{{$user->is_locked}}</td>
                                    <td>{{$user->last_login_at}}</td>
                                    <td>{{$user->creator->name}}</td>
                                    <td>{{\Carbon\Carbon::parse($user->created_at)->format('d M, Y')}}</td>
                                    <td>
                                        <div class="button-list d-flex align-items-center gap-2">
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-info">{{ __('global.update') }}</a>
                                            <a href="{{ route('users.permissions', $user) }}" class="btn btn-sm btn-primary">{{ __('users.view_permissions') }}</a>
                                            <a href="javascript:void(0)" data-action="{{ route('users.destroy', $user) }}" class="btn btn-sm btn-danger btn-delete" data-id="{{$user->id}}">{{ __('global.delete') }}</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                        {{$users->links()}}
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