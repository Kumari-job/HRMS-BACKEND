<?php
return [
    'client_app' => [
        'app_url' => env('APP_URL', 'http://127.0.0.1:8002'),
        'idp_url' => env('IDP_APP_URL', 'http://127.0.0.1:8000'),
        'crm_app_url' => env('CRM_APP_URL', 'http://127.0.0.1:8001'),
        'hrms_app_url' => env('TMS_APP_URL', 'http://127.0.0.1:8003'),
        'tms_app_frontend_url' => env('TMS_APP_FRONTEND_URL','http://127.0.0.1:8002'),
        'hrms_app_frontend_url' => env('HRM_APP_FRONTEND_URL', 'http://127.0.0.1:3003'),
        'sso_host' => env('SSO_HOST','http://127.0.0.1:8080'),
        'company_id' => env('COMPANY_ID',1)
    ],
    'common_token' => env('COMMON_TOKEN')
];
