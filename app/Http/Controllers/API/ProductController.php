<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
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
        // creo un validatore che mi controlla se la richiesta ha determinati campi
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'image' => 'nullable|image',
            'price' => 'required|numeric|between:0.00,999999.99',
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

        // se ce un errore
        if ($validator->fails()) {

            // rispondo con gli errori di compilazione
            return response()->json([
                'success' => false,
                'response' => $validator->errors(),
            ]);
        } else {

            // salvo i dati validati
            $val_data = $validator->validated();

            // se passa il campo image 
            if ($request->has('image')) {

                // inserisco l'immagine nelo storage
                $val_data['image'] = Storage::disk('public')->put('uploads/images', $val_data['image']);
            }

            // controllo se esistono prodotti con lo stesso nome
            $check_slug = Product::where('name', $val_data['name'])->count();

            // creao la variabile dello slug
            $slug = "";

            // se ce piu di uno
            if ($check_slug > 0) {

                // salvo lo slug custom
                $slug = Str::slug($val_data['name'], '-') . "-$check_slug";
            } else {

                // salvo lo slug normale
                $slug = Str::slug($val_data['name'], '-');
            }

            // salvo lo slug nella chiave slug
            $val_data['slug'] = $slug;

            // creo un nuovo prodotto
            $newProduct = Product::create($val_data);

            // rispondo con un messaggio di sucesso
            return response()->json([
                'success' => true,
                'response' => "The $newProduct->name has been created successfully",
            ]);
        }
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
        // creo un check che controlla se esiste il prodotto
        $check_product = Product::where('id', $id)->exists();

        // se esiste
        if ($check_product) {

            // creo un validatore che mi controlla se la richiesta ha determinati campi
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'image' => 'nullable|image',
                'price' => 'required|numeric|between:0.00,999999.99',
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

            // se ce un errore
            if ($validator->fails()) {

                // rispondo con gli errori di compilazione
                return response()->json([
                    'success' => false,
                    'response' => $validator->errors(),
                ]);
            } else {

                // salvo i dati validati
                $val_data = $validator->validated();

                // se passa il campo image 
                if ($request->has('image')) {

                    // inserisco l'immagine nelo storage
                    $val_data['image'] = Storage::disk('public')->put('uploads/images', $val_data['image']);
                }

                // controllo se esistono prodotti con lo stesso nome
                $check_slug = Product::where('name', $val_data['name'])->count();

                // creao la variabile dello slug
                $slug = "";

                // se ce piu di uno
                if ($check_slug > 0) {

                    // salvo lo slug custom
                    $slug = Str::slug($val_data['name'], '-') . "-$check_slug";
                } else {

                    // salvo lo slug normale
                    $slug = Str::slug($val_data['name'], '-');
                }

                // salvo lo slug nella chiave slug
                $val_data['slug'] = $slug;

                // creo un nuovo prodotto
                $newProduct = Product::create($val_data);

                // rispondo con un messaggio di sucesso
                return response()->json([
                    'success' => true,
                    'response' => "The $newProduct->name has been created successfully",
                ]);
            }
        } else {

            // comunica un messaggio per dire che non esiste il prodotto
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
