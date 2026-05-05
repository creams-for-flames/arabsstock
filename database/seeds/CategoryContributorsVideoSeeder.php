<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryContributorsVideoSeeder extends Seeder
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
                ['name' => 'فيديو','slug' => 'فيديو','created_at'=>now()],
            ]
        );
    }
}
