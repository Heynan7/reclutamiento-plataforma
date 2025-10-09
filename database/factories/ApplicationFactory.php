<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\User;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'job_id'  => Job::factory(),
            'cv_file' => 'cvs/' . $this->faker->uuid . '.pdf',
            'cover_letter' => $this->faker->optional()->paragraph(),
            'status' => 'submitted',
            'status_updated_at' => now(),
        ];
    }
}
