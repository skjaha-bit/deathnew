<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'FYRIRHYGGJA API',
    description: 'REST API for FYRIRHYGGJA, a bilingual (EN/IS) digital legacy-planning app. All authenticated endpoints use a Laravel Passport bearer token obtained from /api/v1/auth/register or /api/v1/auth/verify-otp.',
    contact: new OA\Contact(email: 'support@fyrirhyggja.is')
)]
#[OA\Server(url: '/', description: 'Current host')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
#[OA\Schema(
    schema: 'ApiResponse',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Success'),
        new OA\Property(property: 'data', type: 'object', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'ApiErrorResponse',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Something went wrong'),
        new OA\Property(property: 'errors', type: 'object', nullable: true),
    ]
)]
class OpenApiSpec
{
    // This class exists purely to hold app-level OpenAPI attributes for l5-swagger to scan.
}
