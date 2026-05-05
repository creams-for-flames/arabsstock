<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Actor::class, 50)->create();
        factory(App\Models\Photographer::class, 50)->create();
        factory(App\Models\SessionLocation::class, 50)->create();

        // $this->call(CategoryContributorsVectorsSeeder::class);
        
        
        // $this->call(PermissionSeed::class);
        // $this->call(RoleSeed::class);
//        $thisphp artisan db:seed --class=->call(UserSeed::class);
        // $this->call(RoleSeedPivot::class);
//        $this->call(UserSeedPivot::class);

    }
}
