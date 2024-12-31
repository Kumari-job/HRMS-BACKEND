<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => fake()->numerify('##########'),
            'address' => fake()->streetAddress(),
            'date_of_birth' => '1998-01-01',
            'marital_status' => 'single',
            'blood_group' => 'O +ve',
            'religion' => 'hindu',
            'gender' => 'male',
            'company_id' => 1,
        ];
    }
}
