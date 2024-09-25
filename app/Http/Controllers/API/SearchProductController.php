<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchProductController extends Controller
{
    public function search(string $search)
    {
        $products = Product::with('categories')->whereRelation('categories', 'name', '=', $search)->orWhereRelation('categories', 'id', '=', $search)->get();

        if (count($products) > 0) {
            return response()->json([
                'success' => true,
                'response' => $products
            ]);
        } else {
            return response()->json([
                'success' => false,
                'response' => "don't exist products whit that category"
            ]);
        }
    }
}
