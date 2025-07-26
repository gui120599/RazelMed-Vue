<?php

namespace Database\Factories;

use App\Models\Dashboard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dashboard>
 */
class DashboardFactory extends Factory
{

    /**
     * The name of the corresponding model
     * 
     * @var string
     */
    protected $model = Dashboard::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'iframe_link' => 'https://app.powerbi.com/view?r=' . $this->faker->uuid,
            'icon' => $this->faker->randomElement([
                'home',
                'user',
                'settings',
                'calendar',
                'file-text',
                'clipboard',
                'activity',
                'alert-circle',
                'check-circle',
                'x-circle',
            ]),

        ];
    }
}
