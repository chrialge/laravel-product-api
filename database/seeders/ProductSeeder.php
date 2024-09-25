<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(faker $faker_prod): void
    {
        // creo un istanza per un faker esterno
        $faker = \Faker\Factory::create('it_IT');

        // aggiungo come provider la sezione di commercio
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));

        $highlightedProduct = [4, 7, 26, 65, 78, 99];
        $index = 0;


        for ($i = 0; $i < 100; $i++) {
            $newProduct = new Product();
            $newProduct->name = $faker->productName;
            $check_slug = Product::where('name', $newProduct->name)->count();
            $slug = "";
            if ($check_slug > 0) {
                $slug = Str::slug($newProduct->name, '-') . "-$check_slug";
            } else {
                $slug = Str::slug($newProduct->name, '-');
            }
            $newProduct->slug = $slug;
            $newProduct->image = $faker_prod->imageUrl(360, 360, $newProduct->name, true, $newProduct->slug, true, 'jpg');
            $newProduct->price = $faker_prod->randomFloat(2, 50, 1000);
            $newProduct->availability = rand(0, 1);
            $newProduct->color = $faker_prod->colorName();
            $newProduct->description = $faker_prod->text(100);
            if ($highlightedProduct[$index] === $i) {
                $index++;
                $newProduct->highlighted = 1;
            }

            $newProduct->save();
            $newProduct->categories()->attach([rand(1, 12), rand(1, 12), rand(1, 12)]);
        }
    }
}
