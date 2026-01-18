<?php

return [

   'project' => env('APP_PROJECT', ''),

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'DevCraft'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'force_https' => env('FORCE_HTTPS', false),


    /**
     * SMS service configurations
     */

    'idm_sms_sender_name' => env('IDM_SMS_SENDER_NAME' , 'DevCraft'),
    'idm_smsc_login' => env('IDM_SMSC_LOGIN', ''),
    'idm_smsc_password' => env('IDM_SMSC_PASSWORD', ''),
    'idm_mobizone_token' => env('IDM_MOBIZONE_TOKEN', ''),
    'idm_phone_verification_code_send_interval_limit' => env('IDM_PHONE_VERIFICATION_CODE_SEND_INTERVAL_LIMIT', 60),
    'idm_phone_verification_code_rate_limit_per_user_per_day' => env('IDM_PHONE_VERIFICATION_CODE_RATE_LIMIT_PER_USER_PER_DAY', 5),
    'idm_phone_verification_code_rate_limit_per_all_users_per_day' => env('IDM_PHONE_VERIFICATION_CODE_RATE_LIMIT_PER_ALL_USERS_PER_DAY', 25),

    /**
     * TOTP configurations
     */
    'shared_totp_key' => env('APP_SHARED_TOTP_KEY', ''),

    /**
     * Cloudpayments configurations
     */
    'payment_cloudpayments_public_id' => env('PAYMENT_CLOUDPAYMENTS_PUBLIC_ID', ''),
    'payment_cloudpayments_secret' => env('PAYMENT_CLOUDPAYMENTS_SECRET', ''),


    /**
     * TipTopPay configurations
     */
    'payment_tiptoppay_public_id' => env('PAYMENT_TIPTOPPAY_PUBLIC_ID', ''),
    'payment_tiptoppay_secret' => env('PAYMENT_TIPTOPPAY_SECRET', ''),


];
