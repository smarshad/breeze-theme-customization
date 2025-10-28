
# Day 1-2
    Convert existing them into laravel and push to git repo

# Day 3
    Install Breeze
    composer require laravel/breeze --dev
    php artisan breeze:install blade
    php artisan migrate
    php artisan serve
    Check all functionality

# Day 4
    Custommize Breeze theme pages into our existing template
    forgot/reset password screen is pending
    Also add lockscreen feature
    Create Migration for it *add one field into users table is_locked*
        Two types
        1. Session based Where user click on lock link then add key in session is_locked :true and redirect to lock page where user only enter password.
        2. Database --> Where admin lock user permanently. 

# Day 5
    Enable Two Factor Authentication
    user can enable/disable two_factor_auth from profile after login

# Day 6
    Change in Lockmiddleware and apply on route
    Forgot-password screen ready and working