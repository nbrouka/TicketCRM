<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'TicketCRM API',
    version: '1.0',
    description: 'API documentation for TicketCRM application'
)]
#[OA\Server(
    url: 'http://localhost:8103',
    description: 'Development server'
)]
class OpenApiInfoController extends Controller
{
    // This class is used only for OpenAPI documentation generation
}
