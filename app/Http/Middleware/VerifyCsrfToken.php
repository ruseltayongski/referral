<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'login',
        'telemedicine/sync/tsekap',
        'api/refer/patient',
        'mobile/v2/login',
        'mobile/get/v2/facility',
        'mobile/get/v2/province',
        'mobile/get/v2/municipality',
        'mobile/get/v2/barangay'
    ];
}
