<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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

        $val_data = $request->validate([
            'name' => 'required|max:100',
            'image' => 'nullable|image',
            'price' => 'required|between:0.00,99999.99',
            'availability' => 'nullable|boolean',
            'color' => 'nullable|max: 50',
            'description' => 'nullable|text'
        ]);

        return response()->json([
            'success' => 'true',
            'response' => $val_data
        ]);
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
        $check_product = Product::where('id', $id)->exists();

        if ($check_product) {
            $product = Product::where('id', $id)->first();


            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'image' => 'nullable|image',
                'price' => 'required|between:0.00,99999.99',
                'availability' => 'nullable|boolean',
                'color' => 'nullable|max: 50',
                'description' => 'nullable|text'
            ], [
                'name.required' => 'required name',
                'name.max' => 'the length of the name is :max',
                'image.image' => 'accepted only file jpg, jpeg, png',
                'price.required' => 'required price',
                'price.between' => 'The :attribute value :input is not between :min - :max.',
                'color.max' => 'the length of the color is :max',
                'description.text' => 'description only acapted string'
            ]);




            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'response' => $validator->errors(),
                ]);
            } else {

                $val_data = $validator->validated();



                if ($request->has('image')) {
                    $val_data['image'] = Storage::disk('public')->put('uploads/images', $val_data['image']);
                }

                $check_slug = Product::where('name', $val_data['name'])->count();
                $slug = "";

                if ($check_slug > 0) {
                    $slug = Str::slug($val_data['name'], '-') . "-$check_slug";
                } else {
                    $slug = Str::slug($val_data['name'], '-');
                }
                $val_data['slug'] = $slug;

                // return response()->json([
                //     'success' => true,
                //     'response' =>  $val_data,

                // ]);

                $product->update($val_data);

                return response()->json([
                    'success' => true,
                    'response' =>  "the $product->name data have been successfully updated",

                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'response' => "the products don't exist"
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        // controlle se l'id esiste per product 
        $check_product = Product::where('id', $id)->exists();

        if ($check_product) {

            $product = Product::where('id', $id)->first();

            $product->delete();

            return response()->json([
                'success' => true,
                'response' => "has been deleted successfully $product->name"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'response' => "the products don't exist"
            ]);
        }
    }
}
