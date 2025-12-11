@extends('layoutsnew.app')
@push('styles')
<link href="{{asset('backend/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backend/libs/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css" />
@endpush
@push('scripts')
<script src="{{asset('backend/libs/select2/select2.min.js')}}"></script>
@endpush

@php
// Determine if we are in edit mode. Assume $menu is passed for editing.
$isEdit = isset($menu) && $menu->id;
$formAction = $isEdit ? route('menu.update', $menu->id) : route('menu.store');
$pageTitle = $isEdit ? __('menu.edit_title') : __('menu.create_title');
$breadcrumbActive = $isEdit ? 'Edit' : 'New';
$buttonText = $isEdit ? __('global.update') : __('global.save');
@endphp

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('menu.title')}}</a></li>
                            <li class="breadcrumb-item active">{{ $breadcrumbActive }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('menu.title')}}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-3">{{ $pageTitle }}</h4>
                        <a href="{{ route('menu.index') }}" class="btn btn-primary">
                            {{__('global.back')}}
                        </a>
                    </div>
                    <x-alert />

                    <form action="#" method="POST" class="bg-white rounded-lg shadow p-6">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="route" class="block text-gray-700 text-sm font-bold mb-2">Route</label>
                                <input type="text" name="route" id="route" value="{{ old('route') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="url" class="block text-gray-700 text-sm font-bold mb-2">URL</label>
                                <input type="url" name="url" id="url" value="{{ old('url') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="icon" class="block text-gray-700 text-sm font-bold mb-2">Icon</label>
                                <input type="text" name="icon" id="icon" value="{{ old('icon') }}" placeholder="e.g., fa fa-home" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('icon')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="order" class="block text-gray-700 text-sm font-bold mb-2">Order</label>
                                <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('order')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="parent_id" class="block text-gray-700 text-sm font-bold mb-2">Parent Menu</label>
                                <select name="parent_id" id="parent_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- None --</option>
                                    @foreach ($parentMenus as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="permission_id" class="block text-gray-700 text-sm font-bold mb-2">Permission</label>
                                <select name="permission_id" id="permission_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- None --</option>
                                    @foreach ($permissions as $permission)
                                    <option value="{{ $permission->id }}" {{ old('permission_id') == $permission->id ? 'selected' : '' }}>
                                        {{ $permission->name }} ({{ $permission->module }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('permission_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="is_active" class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded">
                                <span class="ml-2 text-gray-700">Active</span>
                            </label>
                            @error('is_active')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Menu
                            </button>
                            <a href="{{ route('menus.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                        </div>
                    </form>

                    <form id="editRoleForm" action="{{ $formAction }}" method="POST" class="data-ajax-submit form-horizontal" @if($isEdit) enctype="multipart/form-data" @endif>
                        @csrf
                        @if($isEdit)
                        @method('PUT')
                        @endif


                        <div class="form-group row">
                            <label for="name" class="col-3 col-form-label">Name</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="name" name="name" value="{{old('name', $menu->name ?? '')}}" placeholder="Name">
                            </div>
                            <label for="route" class="col-3 col-form-label">Route</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="route" name="route" value="{{old('route', $menu->route ?? '')}}" placeholder="Route">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="url" class="col-3 col-form-label">Url (Optional)</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="url" name="url" value="{{old('url', $menu->url ?? '')}}" placeholder="URL">
                            </div>
                            <label for="icon" class="col-3 col-form-label">Icon (Optional)</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="icon" name="icon" value="{{old('icon', $menu->notes ?? '')}}" placeholder="Icon">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="parent_id" class="col-3 col-form-label">Parent Menu (Optional)</label>
                            <div class="col-3">
                                <select class="form-control" id="parent_id" name="parent_id">
                                    <option value="">-- No Parent --</option>
                                    @foreach ($menus as $parentMenu)
                                    @if (isset($menu) && $parentMenu->id == $menu->id)
                                    @continue {{-- Prevent selecting self as parent --}}
                                    @endif
                                    <option value="{{ $parentMenu->id }}" @selected(old('parent_id', $menu->parent_id ?? '') == $parentMenu->id)>
                                        {{ $parentMenu->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="order" class="col-3 col-form-label">Order (Optional)</label>
                            <div class="col-3">
                                <input type="number" class="form-control" id="order" name="order" value="" placeholder="Order">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="parent_id" class="col-3 col-form-label">Permisison</label>
                            <div class="col-3">
                                <select class="form-control" id="parent_id" name="parent_id">
                                    <option value="">-- No Parent --</option>
                                    @foreach ($menus as $parentMenu)
                                    @if (isset($menu) && $parentMenu->id == $menu->id)
                                    @continue {{-- Prevent selecting self as parent --}}
                                    @endif
                                    <option value="{{ $parentMenu->id }}" @selected(old('parent_id', $menu->parent_id ?? '') == $parentMenu->id)>
                                        {{ $parentMenu->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>


                        <div class="form-group mb-0 row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-info waves-effect waves-light">{{ $buttonText }}</button>
                                <button type="reset" class="btn btn-danger waves-effect waves-light">{{ __('global.reset') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('backend/js/ajax-form-submit.js') }}"></script>
<script src="{{ asset('backend/js/menu.js') }}"></script>
@endpush