<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{

    public function index()
    {
        $products = Product::with('categories')->get();

        if (count($products) > 0) {

            return response()->json([
                'success' => true,
                'response' => $products,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'response' => '404 Sorry nothing found'
            ]);
        }
    }
}
