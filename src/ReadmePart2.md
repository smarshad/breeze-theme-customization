# Day1 Basic setup for RBAC create Permission(CRUD mixed normal/ajax)

1st install laravel spatie package
    composer require spatie/laravel-permission
    publish php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
    php artisan config:clear   
            or
    php artisan optimize:clear

    php artisan migrate

2nd create view -- admin/permissions [index,create,edit,show]
3rd add in app.layout <meta name="csrf-token" content="{{ csrf_token() }}"> for ajax
4th create PermissionController, alter permision and role table add description column
5th create request for StorePermissionRequest
6th create request for UpdatePermissionRequest
7th create BaseFormRequest
    for to create failedValidation in case of ajax  
    protected function failedValidation(Validator $validator)
    {
        // If the request expects JSON (AJAX, fetch(), axios)
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422)
            );
        }

        // Otherwise use Laravel's normal behavior (redirect back with errors)
        parent::failedValidation($validator);
    }   

# Day2 
    Refactor Permisison controller centralised log data
    Add one column into permission table (module)
    drop unique([name,guard_name]) and add('name','module', 'guard_name')
    Change Validation store and update permission request
    Create Permission model and extend with spatie permission model
    change config/permission.php change model to our newly created model
    clear cache
    Delete Using Ajax
    New function in helper for log
    Create Role Controller (resource)
    Create Views for role
    add route for roles
    add menu in left
    change in common ajax-form-submit.js if (field === 'permissions') {$('.alert-danger').html(messages[0]).show();}
    complete CRUD Role module using AJAX
