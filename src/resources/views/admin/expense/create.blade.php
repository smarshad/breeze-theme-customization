@extends('layoutsnew.app')
@push('styles')
<link href="{{asset('backend/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backend/libs/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css" />
@endpush
@push('scripts')
<script src="{{asset('backend/libs/select2/select2.min.js')}}"></script>
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('expense.title')}}</a></li>
                            <li class="breadcrumb-item active">New</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('expense.title')}}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-3">{{ __('expense.create_title')}}</h4>
                        <a href="{{ route('expense.index') }}" class="btn btn-primary">
                            {{__('global.back')}}
                        </a>
                    </div>
                    <x-alert />

                    <form id="editRoleForm" action="{{route('expense.store')}}" method="POST" class="data-ajax-submit form-horizontal">
                        @csrf
                        <div class="form-group row">
                            <label for="role" class="col-3 col-form-label">Select Category</label>
                            @if($categories->isNotEmpty())
                            <div class="col-3">
                                <select name="category_id" id="category_id" class="form-control" data-toggle="select2">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <label for="role" class="col-3 col-form-label">Select Expense Type</label>
                            @if($expenseTypes->isNotEmpty())
                            <div class="col-3">
                                <select name="expense_type_id" id="expense_type_id" class="form-control" data-toggle="select2">
                                    <option value="">Select Expense Type</option>
                                    @foreach($expenseTypes as $expenseType)
                                    <option value="{{$expenseType->id}}">{{$expenseType->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                        <div class="form-group row">
                            <label for="role" class="col-3 col-form-label">Select Payment Method</label>
                            @if($paymentMethods->isNotEmpty())
                            <div class="col-3">
                                <select name="payment_method_id" id="payment_method_id" class="form-control" data-toggle="select2">
                                    <option value="">Select Payment Method</option>
                                    @foreach($paymentMethods as $paymentMethod)
                                    <option value="{{$paymentMethod->id}}">{{$paymentMethod->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <label class="col-md-3 col-form-label" for="expense_date">Expense Date</label>
                            <div class="col-md-3">
                                <input class="form-control" id="expense_date" name="expense_date" type="date" name="date">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="amount" class="col-3 col-form-label">Amount</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="amount" name="amount" value="{{old('amount')}}" placeholder="create user">
                            </div>
                            <label for="description" class="col-3 col-form-label">Description</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="description" name="description" value="{{old('description')}}" placeholder="Description">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="cashback" class="col-3 col-form-label">Cashback</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="cashback" name="cashback" value="{{old('cashback')}}" placeholder="0.0">
                            </div>
                            <label for="notes" class="col-3 col-form-label">Notes</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="notes" name="notes" value="{{old('notes')}}" placeholder="Notes">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="uploaded_file" class="col-3 col-form-label">File Path</label>
                            <div class="col-3">
                                <input type="file" class="form-control" id="uploaded_file" name="uploaded_file">
                            </div>
                        </div>
                        <div class="form-group mb-0 row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-info waves-effect waves-light">{{ __('global.save') }}</button>
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
<script src="{{ asset('backend/js/expense.js') }}"></script>
@endpush