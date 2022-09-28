<?php

namespace App\Http\Controllers;

use App\Http\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * @var PaymentService
     */
    private $oService;

    /**
     * @param PaymentService $oService
     */
    public function __construct(PaymentService $oService)
    {
        $this->oService = $oService;
    }

    /**
     * @param Request $oRequest
     * @return JsonResponse
     */
    public function createCheckout(Request $oRequest): JsonResponse
    {
        return response()->json($this->oService->createCheckout($oRequest->input('total')));
    }
}
