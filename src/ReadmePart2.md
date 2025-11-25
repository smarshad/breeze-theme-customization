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
