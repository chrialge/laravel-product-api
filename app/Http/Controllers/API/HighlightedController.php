<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HighlightedController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products/highlighted/{highlighted}",
     *     summary= "response products highlighted",
     *      tags = {"Search"},
     *     @OA\Parameter( 
     *          name = "highlighted",
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
    public function index(string $highlighted)
    {

        $products = Product::with('categories')->where('highlighted', $highlighted)->get();

        if (count($products) > 0) {
            return response()->json([
                'success' => true,
                'response' => $products
            ]);
        } else {
            return response()->json([
                'success' => false,
                'response' => "Don't insert value different to 0 and 1"
            ]);
        }
    }
}
