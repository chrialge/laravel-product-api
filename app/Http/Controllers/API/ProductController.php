<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        // controlle se l'id esiste per product 
        $check_product = Product::where('id', $id)->exists();
        if ($check_product) {
            $product = Product::where('id', $id)->with('categories')->get();

            return response()->json([
                'success' => true,
                'response' => $product
            ]);
        } else {
            return response()->json([
                'success' => false,
                'response' => "the products don't exist"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}
}
