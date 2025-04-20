<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;

/**
 * @OA\Info(title="DT Translation API", version="1.0")
 */
abstract class Controller
{
    use ApiResponse;
}
