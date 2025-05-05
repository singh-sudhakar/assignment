<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 100) as $i) {
            $date = $faker->dateTimeBetween('-2 months', 'now');
            Patient::create([
                'patient_id' => Str::uuid(),
                'name' => $faker->name,
                'group' => $faker->randomElement(['A', 'B', 'C', 'D']),
                'wait_time' => $faker->numberBetween(5, 120), // in minutes
                'consultation_date' => $date,
                'week' => (int) $date->format('W'),
                'month' => $date->format('F'),
            ]);
        }
    }
}
