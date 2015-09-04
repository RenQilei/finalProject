<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ArticleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for($articleLoop = 0; $articleLoop < 5000; $articleLoop++) {

            DB::table('articles')->insert([
                'title' => $faker->sentence($nbWords = $faker->numberBetween($min = 3, $max = 15)),
                'author' => 1,
                'category_id' => 1,
                'template_id'   => 0,
                'created_at' => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ]);

            $articleSectionAmount = $faker->numberBetween($min = 3, $max = 15);
            $articleSectionArray = $faker->paragraphs($nb = $articleSectionAmount);
            $articleContent = "";
            for($articleSectionLoop = 0; $articleSectionLoop < $articleSectionAmount; $articleSectionLoop++) {
                $articleContent = $articleContent."<p>".$articleSectionArray[$articleSectionLoop]."</p>";
            }

            DB::table('article_sections')->insert([
                'content' => $articleContent,
                'article_id' => $articleLoop + 1,
                'created_at' => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ]);
        }
    }
}
