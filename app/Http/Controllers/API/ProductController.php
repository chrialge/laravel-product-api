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
     * @OA\Get(
     *     path="/api/products",
     *     summary= "response with all products",
     *      tags = {"Products"},
     *     @OA\Response(
     *         response="200",
     *         description="the list of products",
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
    public function index()
    {
        // salvoo i prodotti e le sue categorie
        $products = Product::with('categories')->orderByDesc('id')->get();

        // se ha piu di zero risultati
        if (count($products) > 0) {

            // return di una risposta con i prodotti
            return response()->json([
                'success' => true,
                'response' => $products,
            ]);
        } else {

            // return di una risposta negativa
            return response()->json([
                'success' => false,
                'response' => '404 Sorry nothing found'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary= "create new product.",
     *      tags = {"Products"},
     * 
     *      @OA\RequestBody(
     *          
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="Hammer",
     *                      maxLength=100,
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="string",
     *                      format="binary"
     *                  ),
     *                  @OA\Property(
     *                      property="price",
     *                      type="number",
     *                      maximum=99999.99,
     *                      example=20.00,
     *                      minimum=0.00,
     *                  ),
     *                  @OA\Property(
     *                      property="availability",
     *                      type="number",
     *                      minimum=0,
     *                      maximum=1,
     *                      example=1,
     *                  ),
     *                  @OA\Property(
     *                      property="color",
     *                      type="string",
     *                      example="red",
     *                      maxLength=50,
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      example="there is functional hammer",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="category_id",
     *                      type="object",
     *                      example={1,2,3,4,5}
     *                              
     *                  ),
     *              ),
     *      ),
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="uptade of the product",
     *         @OA\MediaType(
     *              mediaType="application/json"
     *         ),
     *     ),
     *      @OA\Response(
     *         response="404",
     *         description="find anythings"
     *     )
     * )
     */



    public function store(Request $request)
    {
        // creo un validatore che mi controlla se la richiesta ha determinati campi
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'image' => 'nullable|file',
            'price' => 'required|numeric|between:0.00,999999.99',
            'availability' => 'nullable|boolean',
            'color' => 'nullable|max: 50',
            'description' => 'nullable|string',
            'category_id' => 'nullable'
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

            // catturo las tringa e la trasformo in una stringa
            $val_data['category_id'] = explode(",", $val_data['category_id']);




            // se passa il campo image 
            if (isset($val_data['image'])) {

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


            // se passa le categorie
            if ($request->has('category_id')) {

                // ciclo per tutte le categorie
                foreach ($val_data['category_id'] as $category) {
                    // creo nuovi record nella tabella pivot
                    $newProduct->categories()->attach($category);
                }
            }

            // rispondo con un messaggio di sucesso
            return response()->json([
                'success' => true,
                'response' => $val_data,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary= "response with all products",
     *      tags = {"Products"},
     *     @OA\Parameter( 
     *          name = "id",
     *          in = "path",
     *          description = "id product",
     *          required = true,
     *         ),
     *     @OA\Response(
     *         response="200",
     *         description="single product",
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
    public function show(string $id)
    {

        // controlle se l'id esiste per product 
        $check_product = Product::where('id', $id)->exists();

        // se esiste il prodotto
        if ($check_product) {

            // salvo il prodotto
            $product = Product::where('id', $id)->with('categories')->first();

            // rispondo con il prodotto
            return response()->json([
                'success' => true,
                'response' => $product
            ]);
        } else {

            // rispondo che il prodotto non esiste
            return response()->json([
                'success' => false,
                'response' => "the products don't exist"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary= "modify single product.",
     *      tags = {"Products"},
     *      @OA\Parameter( 
     *          name = "id",
     *          in = "path",
     *          description = "id of the product",
     *          required = true,
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              encoding="image",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="Hammer",
     *                      maxLength=100,
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="string",
     *                      format="binary"
     *                  ),
     *                  @OA\Property(
     *                      property="price",
     *                      type="number",
     *                      maximum=99999.99,
     *                      example=20.00,
     *                      minimum=0.00,
     *                  ),
     *                  @OA\Property(
     *                      property="availability",
     *                      type="number",
     *                      minimum=0,
     *                      maximum=1,
     *                      example=1,
     *                  ),
     *                  @OA\Property(
     *                      property="color",
     *                      type="string",
     *                      example="red",
     *                      maxLength=50,
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      example="there is functional hammer",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="category_id",
     *                      type="object",
     *                      example={1,2,3,4,5}
     *                              
     *                  ),
     *              ),
     *      ),
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="uptade of the product",
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

    public function update(Request $request, string $id)
    {
        // creo un check che controlla se esiste il prodotto
        $check_product = Product::where('id', $id)->exists();


        $product = Product::where('id', $id)->first();

        // se esiste
        if ($check_product) {

            // creo un validatore che mi controlla se la richiesta ha determinati campi
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'image' => 'nullable',
                'price' => 'required|numeric|between:0.00,999999.99',
                'availability' => 'nullable|boolean',
                'highlighted' => 'nullable|boolean',
                'color' => 'nullable|max: 50',
                'description' => 'nullable|string',
                'category_id' => 'nullable'
            ], [
                'name.required' => 'required name',
                'name.max' => 'the length of the name is :max',
                'image.image' => 'accepted only file jpg, jpeg, png',
                'price.required' => 'required price',
                'price.between' => 'The :attribute value :input is not between :min - :max.',
                'color.max' => 'the length of the color is :max',
                'description.text' => 'description only acapted string',

            ]);
            // rispondo con un messaggio di sucesso


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
                if (isset($val_data['image'])) {

                    $val_data['image'] = base64_encode($val_data['image']);
                    if ($product->image) {
                        Storage::disk('public')->delete($product->image);
                        $val_data['image'] = Storage::disk('public')->put('uploads/images', $val_data['image']);
                    }

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
                $categories = '';

                if (isset($val_data['category_id'])) {
                    $val_data['category_id'] = explode(",", $val_data['category_id']);
                    $categories = $val_data['category_id'];
                    unset($val_data['category_id']);
                };


                // aggiorna il prodotto
                Product::where('id', $id)->update($val_data);

                // il nuovo prodotto
                $newProduct = Product::where('id', $id)->first();


                if ($categories) {
                    $newProduct->categories()->detach();
                    foreach ($categories as $category) {
                        $newProduct->categories()->attach($category);
                    }
                }
                return response()->json([
                    'success' => true,
                    'response' => "the $newProduct->name has been updated!",
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

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary= "response with all products",
     *      tags = {"Products"},
     *     @OA\Parameter( 
     *          name = "id",
     *          in = "path",
     *          description = "id of the product",
     *          required = true,
     *         ),
     *     @OA\Response(
     *         response="200",
     *         description="message of success",
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
    public function destroy(string $id)
    {

        // controlle se l'id esiste per product 
        $check_product = Product::where('id', $id)->exists();

        // se esiste
        if ($check_product) {

            // prendo il singolo prodotto
            $product = Product::where('id', $id)->first();

            // cancello il prodotto
            $product->delete();

            // rispondo con un messaggio di comferma
            return response()->json([
                'success' => true,
                'response' => "has been deleted successfully $product->name"
            ]);
        } else {

            // rispondo che il prodotto non esiste
            return response()->json([
                'success' => false,
                'response' => "the products don't exist"
            ]);
        }
    }
}
