<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\Lga;

class StatesLgasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statesWithLgas = [
            'Lagos' => ['Alimosho', 'Ajeromi-Ifelodun', 'Kosofe', 'Mushin', 'Oshodi-Isolo', 'Ojo', 'Ikorodu', 'Surulere'],
            'Abuja (FCT)' => ['Abaji', 'Bwari', 'Gwagwalada', 'Kuje', 'Kwali', 'Municipal Area Council'],
            'Kano' => ['Kano Municipal', 'Fagge', 'Dala', 'Gwale', 'Tarauni', 'Nassarawa', 'Kumbotso', 'Ungogo'],
            'Rivers' => ['Port Harcourt', 'Obio/Akpor', 'Okrika', 'Ogu/Bolo', 'Eleme', 'Tai', 'Gokana', 'Khana'],
            'Oyo' => ['Ibadan North', 'Ibadan South-West', 'Ibadan South-East', 'Ibadan North-West', 'Ibadan North-East'],
            'Kaduna' => ['Kaduna North', 'Kaduna South', 'Chikun', 'Kajuru', 'Igabi', 'Zaria'],
        ];

        foreach ($statesWithLgas as $stateName => $lgas) {
            $state = State::firstOrCreate(
                ['state' => $stateName],
                ['status' => 1]
            );

            foreach ($lgas as $lgaName) {
                Lga::firstOrCreate(
                    ['state_id' => $state->id, 'lga' => $lgaName],
                    ['status' => 1]
                );
            }
        }
    }
}
