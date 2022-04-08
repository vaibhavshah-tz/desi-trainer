<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Response\ApiResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Current active menu of sidebar
     * 
     * @var string
     */
    public $apiResponse;

    /**
     * Instantiate a new controller instance
     * 
     * @return void
     */
    public function __construct(ApiResponse $apiResponse) {
        $this->apiResponse = $apiResponse;
    }

}
