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


        for ($i = 0; $i < 100; $i++) {
            $newProduct = new Product();
            $newProduct->name = $faker->productName;
            $newProduct->slug = Str::slug($newProduct->name, '-');
            $newProduct->image = $faker_prod->imageUrl(360, 360, $newProduct->name, true, $newProduct->slug, true, 'jpg');
            $newProduct->price = $faker_prod->randomFloat(2, 50, 1000);
            $newProduct->availability = rand(0, 1);
            $newProduct->color = $faker_prod->colorName();
            $newProduct->description = $faker_prod->text(100);
            $newProduct->save();
            $newProduct->categories()->attach([rand(1, 12), rand(1, 12), rand(1, 12)]);
        }
    }
}
