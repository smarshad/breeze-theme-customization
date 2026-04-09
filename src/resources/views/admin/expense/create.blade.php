@extends('layoutsnew.app')
@push('styles')
<link href="{{asset('backend/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backend/libs/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css" />
@endpush
@push('scripts')
<script src="{{asset('backend/libs/select2/select2.min.js')}}"></script>
@endpush

@php
// Determine if we are in edit mode. Assume $expense is passed for editing.
$isEdit = isset($expense) && $expense->id;
$formAction = $isEdit ? route('expense.update', $expense->id) : route('expense.store');
$pageTitle = $isEdit ? __('expense.edit_title') : __('expense.create_title');
$breadcrumbActive = $isEdit ? 'Edit' : 'New';
$buttonText = $isEdit ? __('global.update') : __('global.save');
$canSubmit = $isEdit ? Gate::allows('update', $expense) : Gate::allows('create', App\Models\Expense::class);
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('expense.title')}}</a></li>
                            <li class="breadcrumb-item active">{{ $breadcrumbActive }}</li>
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
                        <h4 class="header-title mb-3">{{ $pageTitle }}</h4>
                        <a href="{{ route('expense.index') }}" class="btn btn-primary">
                            {{__('global.back')}}
                        </a>
                    </div>
                    <x-alert />

                    <form id="editRoleForm" action="{{ $formAction }}" method="POST" class="data-ajax-submit form-horizontal" @if($isEdit) enctype="multipart/form-data" @endif>
                        @csrf
                        @if($isEdit)
                        @method('PUT')
                        @endif

                        @if(!$canSubmit)
                            <p class="text-danger d-block mt-1">
                                You do not have permission to {{ $isEdit ? 'update' : 'create' }} this expense.
                            </p>
                        @endif
                        <div class="form-group row">
                            <label for="category_id" class="col-3 col-form-label">Select Category</label>
                            @if($categories->isNotEmpty())
                            <div class="col-3">
                                <select name="category_id" id="category_id" class="form-control" data-toggle="select2">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{$category->id}}" @if(old('category_id', $expense->category_id ?? null) == $category->id) selected @endif>{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <label for="expense_type_id" class="col-3 col-form-label">Select Expense Type</label>
                            @if($expenseTypes->isNotEmpty())
                            <div class="col-3">
                                <select name="expense_type_id" id="expense_type_id" class="form-control" data-toggle="select2">
                                    <option value="">Select Expense Type</option>
                                    @foreach($expenseTypes as $expenseType)
                                    <option value="{{$expenseType->id}}" @if(old('expense_type_id', $expense->expense_type_id ?? null) == $expenseType->id) selected @endif>{{$expenseType->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                        <div class="form-group row">
                            <label for="payment_method_id" class="col-3 col-form-label">Select Payment Method</label>
                            @if($paymentMethods->isNotEmpty())
                            <div class="col-3">
                                <select name="payment_method_id" id="payment_method_id" class="form-control" data-toggle="select2">
                                    <option value="">Select Payment Method</option>
                                    @foreach($paymentMethods as $paymentMethod)
                                    <option value="{{$paymentMethod->id}}" @if(old('payment_method_id', $expense->payment_method_id ?? null) == $paymentMethod->id) selected @endif>{{$paymentMethod->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <label class="col-md-3 col-form-label" for="expense_date">Expense Date</label>
                            <div class="col-md-3">
                                @php
                                $defaultDate = date('Y-m-d');
                                $expenseDateValue = old('expense_date', $expense->expense_date ?? $defaultDate);
                                // Ensure the date is formatted as YYYY-MM-DD for the HTML date input
                                if ($expenseDateValue && !is_string($expenseDateValue)) {
                                // Assuming it's a Carbon instance or similar object that can be formatted
                                $expenseDateValue = $expenseDateValue->format('Y-m-d');
                                } elseif ($expenseDateValue) {
                                // If it's a string (e.g., "2025-12-09 00:00:00"), format it
                                $expenseDateValue = date('Y-m-d', strtotime($expenseDateValue));
                                }
                                @endphp
                                <input class="form-control" id="expense_date" name="expense_date" type="date" value="{{ $expenseDateValue }}">

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="amount" class="col-3 col-form-label">Amount</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="amount" name="amount" value="{{old('amount', $expense->amount ?? '')}}" placeholder="Amount">
                            </div>
                            <label for="description" class="col-3 col-form-label">Description</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="description" name="description" value="{{old('description', $expense->description ?? '')}}" placeholder="Description">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="cashback" class="col-3 col-form-label">Cashback</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="cashback" name="cashback" value="{{old('cashback', $expense->cashback ?? '')}}" placeholder="0.0">
                            </div>
                            <label for="notes" class="col-3 col-form-label">Notes</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="notes" name="notes" value="{{old('notes', $expense->notes ?? '')}}" placeholder="Notes">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="uploaded_file" class="col-3 col-form-label">File Path</label>
                            <div class="col-3">
                                <input type="file" class="form-control" id="uploaded_file" name="uploaded_file">
                                @if($isEdit && $expense->uploaded_file)
                                <small class="form-text text-muted">Current file: <a href="{{ asset('path/to/your/files/' . $expense->uploaded_file) }}" target="_blank">{{ $expense->uploaded_file }}</a></small>
                                @endif
                            </div>

                            {{-- Existing file preview (edit mode) --}}
                            @if($isEdit && $expense->file_path)
                            <label class="col-3 col-form-label">Old File</label>
                            <div class="col-3">
                                @php
                                // Build public URL for file on 'public' disk. Adjust if you use a different disk.
                                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($expense->file_path);
                                $ext = strtolower(pathinfo($expense->file_path, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg','jpeg','png']);
                                $isPdf = $ext === 'pdf';
                                @endphp
                                @if($isImage)
                                <div class="mt-1">
                                    <a href="{{ $url }}" target="_blank" rel="noopener">
                                        <img src="{{ $url }}"
                                            alt="Current Image"
                                            class="img-thumbnail"
                                            style="max-width:180px; max-height:180px;">
                                    </a>
                                </div>

                                {{-- PDF PREVIEW --}}
                                @elseif($isPdf)
                                <div class="mt-2" style="max-width:300px; max-height:300px; overflow:hidden; border:1px solid #ddd;">
                                    <object data="{{ $url }}" type="application/pdf" width="100%" height="250">
                                        <p>
                                            PDF preview unavailable.
                                            <a href="{{ $url }}" target="_blank">Click to view PDF</a>
                                        </p>
                                    </object>
                                </div>
                                <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                    View PDF
                                </a>

                                {{-- OTHER FILE TYPES (VIEW ONLY) --}}
                                @else
                                <p class="mb-1">File: <a href="{{ $url }}" target="_blank">{{ basename($expense->file_path) }}</a></p>
                                @endif
                            </div>
                            @endif

                            <label for="bank_account" class="col-3 col-form-label">Bank Account</label>
                            <div class="col-3">
                                <input type="text" class="form-control" id="bank_account" name="bank_account" value="{{old('bank_account', $expense->bank_account ?? '')}}" placeholder="Bank Account">
                            </div>
                        </div>
                       
                        <div class="form-group mb-0 row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-info waves-effect waves-light" @disabled(!$canSubmit)>{{ $buttonText }}</button>
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