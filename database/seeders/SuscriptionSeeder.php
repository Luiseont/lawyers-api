<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Suscription;
use App\Models\SuscriptionType;

class SuscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suscriptions = [
            ['user_id' => User::all()->where('role','lawyer')->random()->id ,'type_id'=> SuscriptionType::all()->random()->id, 'amount' => number_format(rand(10, 160), 2)],
            ['user_id' => User::all()->where('role','lawyer')->random()->id ,'type_id'=> SuscriptionType::all()->random()->id, 'amount' => number_format(rand(10, 160), 2)],
            ['user_id' => User::all()->where('role','lawyer')->random()->id ,'type_id'=> SuscriptionType::all()->random()->id, 'amount' => number_format(rand(10, 160), 2)],
            ['user_id' => User::all()->where('role','lawyer')->random()->id ,'type_id'=> SuscriptionType::all()->random()->id, 'amount' => number_format(rand(10, 160), 2)],
            ['user_id' => User::all()->where('role','lawyer')->random()->id ,'type_id'=> SuscriptionType::all()->random()->id, 'amount' => number_format(rand(10, 160), 2)],
            ['user_id' => User::all()->where('role','lawyer')->random()->id ,'type_id'=> SuscriptionType::all()->random()->id, 'amount' => number_format(rand(10, 160), 2)],
        ];

        foreach($suscriptions as $suscription)
        {
            suscription::updateOrCreate($suscription);
        }
    }
}
