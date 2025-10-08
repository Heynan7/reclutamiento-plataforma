<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'title'       => $this->faker->jobTitle(),
            'description' => $this->faker->paragraph(),
            'is_open'     => true,
            // Crea un usuario para created_by y evita constraints FK
            'created_by'  => User::factory(),
        ];
    }
}
