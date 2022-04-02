<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuscriptionType;

class SuscriptionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'Monthly'],
            ['name' => 'Yearly'],
            ['name' => 'Weekly'],
        ];

        foreach($types as $type)
        {
            SuscriptionType::updateOrCreate(['name' => $type['name']]);
        }
    }
}
