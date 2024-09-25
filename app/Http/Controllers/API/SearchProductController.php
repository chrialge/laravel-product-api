<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products/categories/{search}",
     *     summary= "response of the product with category id or name to give",
     *      tags = {"Search"},
     *     @OA\Parameter( 
     *          name = "search",
     *          in = "path",
     *          description = " category id or name",
     *          required = true,
     *         ),
     *     @OA\Response(
     *         response="200",
     *         description="prducts with this category",
     *         @OA\MediaType(
     *              mediaType="application/json"
     *         ),
     *     ),
     *      @OA\Response(
     *         response="404",
     *         description="Not Found"
     *     )
     * )
     */
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
