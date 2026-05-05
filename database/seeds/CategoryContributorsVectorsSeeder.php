<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryContributorsVectorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category_contributors')->insert(
            [
                ['name' => 'فيكتور','slug' => 'فيكتور','created_at'=>now()]
            ]
        );
    }
}
