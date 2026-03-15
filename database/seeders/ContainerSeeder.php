<?php

namespace Database\Seeders;

use App\Enums\BinderType;
use App\Models\Container;
use App\Models\DefaultCard;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $cardIds = DefaultCard::inRandomOrder()->limit(10)->pluck('id');

        $containers = [
            ['name' => 'Legacy Staples',        'type' => BinderType::Binder,    'description' => 'Force of Will, Wasteland, etc.'],
            ['name' => 'Modern Deck: Murktide', 'type' => BinderType::Deckbox,   'description' => 'UR Murktide Regent'],
            ['name' => 'Atraxa',                'type' => BinderType::Deckbox,   'description' => 'Atraxa, Praetors\' Voice superfriends'],
            ['name' => 'Kithkin',               'type' => BinderType::Deckbox,   'description' => 'Brigid, Clachan\'s Heart'],
            ['name' => 'Trade Binder',          'type' => BinderType::Binder,    'description' => 'Cards available for trade'],
            ['name' => 'Cube 360',              'type' => BinderType::Binder,    'description' => 'Powered vintage cube'],
            ['name' => 'MH3 Display',           'type' => BinderType::Display,   'description' => 'Assorted Modern Horizons 3 cards'],
            ['name' => 'Toploaders',            'type' => BinderType::Toploader, 'description' => 'High-value singles in toploaders'],
            ['name' => 'Holiday Gift Tin',      'type' => BinderType::Tin,       'description' => '2024 holiday promo tin'],
            ['name' => 'Misc Tokens',           'type' => BinderType::Other,     'description' => 'Token and emblem collection', 'custom_type' => 'Token Box'],
        ];

        foreach ($containers as $index => $data) {
            Container::create([
                'user_id'         => $user->id,
                'name'            => $data['name'],
                'type'            => $data['type'],
                'description'     => $data['description'],
                'custom_type'     => $data['custom_type'] ?? null,
                'default_card_id' => $cardIds[$index] ?? null,
                'sort_order'      => $index + 1,
            ]);
        }
    }
}
