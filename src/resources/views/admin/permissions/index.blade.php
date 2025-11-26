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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('permissions.title')}}</a></li>
                            <li class="breadcrumb-item active">All</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('permissions.title')}}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-3">{{ __('permissions.all') }}</h4>
                        <a href="{{ route('permissions.create') }}"
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
                                        <th>Description</th>
                                        <th>Guard Name</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            <tbody>
                                @if($permissions->isNotEmpty())
                                @foreach($permissions as $permission)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$permission->name}}</td>
                                    <td>{{$permission->description}}</td>
                                    <td>{{$permission->guard_name}}</td>
                                    <td>{{\Carbon\Carbon::parse($permission->created_at)->format('d M, Y')}}</td>
                                    <td>
                                        <div class="button-list d-flex align-items-center gap-2">
                                            <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-info">{{ __('global.update') }}</a>
                                            <a href="javascript:void(0)" data-action="{{ route('permissions.destroy', $permission) }}" class="btn btn-sm btn-danger btn-delete" data-id="{{$permission->id}}">{{ __('global.delete') }}</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                        {{$permissions->links()}}
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