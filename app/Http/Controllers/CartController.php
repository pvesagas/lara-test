<?php

namespace App\Http\Controllers;

use App\Repositories\ProductRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * @var Request
     */
    private $oRequest;

    /**
     * @var ProductRepository
     */
    private $oRepository;

    /**
     * @param ProductRepository $oRepository
     * @param Request $oRequest
     */
    public function __construct(ProductRepository $oRepository, Request $oRequest)
    {
        $this->oRequest = $oRequest;
        $this->oRepository = $oRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getCart()
    {
        return response()->json([
            'result' => true,
            'data'   => Session::get('cart')
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function addToCart(): JsonResponse
    {
        $iProductId = $this->oRequest->get('product');
        $oProduct = $this->oRepository->getProduct($iProductId);
        if (empty($oProduct) === true) {
            return response()->json(
                [
                    'result' => false,
                    'error'  => 'Product not found'
                ],
                400
            );
        }

        $aCart = Session::get('cart');
        $aCart[$iProductId] = [
            'id'    => $oProduct->id,
            'name'  => $oProduct->name,
            'price' => $oProduct->price,
            'img'   => $oProduct->img_path,
            'count' => 1,
        ];

        Session::put('cart', $aCart);
        return response()->json([
            'result' => true,
            'data'   => Session::get('cart')
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function updateCart()
    {
        $aCart = Session::get('cart');
        $aCart[$this->oRequest->get('product')]['count'] = $this->oRequest->get('count');

        Session::put('cart', $aCart);
        return response()->json([
            'result' => true,
            'data'   => Session::get('cart')
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function removeCartItem()
    {
        $aCart = Session::get('cart');
        unset($aCart[$this->oRequest->get('product')]);

        Session::put('cart', $aCart);
        return response()->json([
            'result' => true,
            'data'   => Session::get('cart')
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function clearCart()
    {
        Session::forget('cart');
        return response()->json([
            'result' => empty(Session::get('cart')),
        ]);
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function displaySuccess(): View
    {
        Session::forget('cart');
        return view('success');
    }
}
