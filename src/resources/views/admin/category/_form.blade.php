@php
    $isEdit = $category?->exists;
    $action = $isEdit ? route('category.update', $category) : route('category.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<form id="categoryForm" action="{{ $action }}" method="{{$method}}" class="data-ajax-submit form-horizontal">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    <div class="form-group row">
        <label class="col-md-4 col-form-label" for="name">Name</label>
        <div class="col-md-8">
            <input type="text" id="name" name="name" class="form-control" value="{{ $category && $category->exists ? $category->name : '' }}" placeholder="Lunch, Dinner">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-4 col-form-label" for="color_code">Color Code</label>
        <div class="col-md-8">
            <input type="text" id="color_code" name="color_code" class="form-control" maxlength="6" value="{{ isset($category) ? $category->color_code : '' }}" placeholder="000000">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-4 col-form-label" for="description">Description</label>
        <div class="col-md-8">
            <textarea id="description" name="description" class="form-control" placeholder="description">{{ isset($category) ? $category->description : '' }}</textarea>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-4 col-form-label" for="is_active">Is Active</label>
        <div class="col-md-8 pt-10">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ isset($category) && $category->is_active ? 'checked' : '' }}>
                <label class="custom-control-label" for="is_active">&nbsp;</label>
            </div>
        </div>
    </div>
    <!-- <div class="form-group mb-0 row">
        <div class="offset-3 col-9">
            <button type="submit" class="btn btn-info waves-effect waves-light">{{ __('global.new') }}</button>
        </div>
    </div> -->
</form>