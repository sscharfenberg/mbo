<?php

namespace Database\Seeders;

use App\Enums\BinderType;
use App\Enums\CardLanguage;
use App\Models\CardStack;
use App\Models\Container;
use App\Models\DefaultCard;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Requires default_cards to be populated first (via `php artisan scryfall:update`).
     */
    public function run(): void
    {
        $user = User::first();

        // Wipe existing card stacks and containers so the seeder is idempotent.
        CardStack::where('user_id', $user->id)->delete();
        Container::where('user_id', $user->id)->delete();

        $cardIds = DefaultCard::inRandomOrder()->limit(10)->pluck('id');

        $containers = [
            ['name' => 'Legacy Staples', 'type' => BinderType::Binder, 'description' => 'Force of Will, Wasteland, etc.'],
            ['name' => 'Modern Deck: Murktide', 'type' => BinderType::Deckbox, 'description' => 'UR Murktide Regent'],
            ['name' => 'Atraxa', 'type' => BinderType::Deckbox, 'description' => 'Atraxa, Praetors\' Voice superfriends'],
            ['name' => 'Kithkin', 'type' => BinderType::Deckbox, 'description' => 'Brigid, Clachan\'s Heart'],
            ['name' => 'Trade Binder', 'type' => BinderType::Binder, 'description' => 'Cards available for trade'],
            ['name' => 'Cube 360', 'type' => BinderType::Binder, 'description' => 'Powered vintage cube'],
            ['name' => 'MH3 Display', 'type' => BinderType::Display, 'description' => 'Assorted Modern Horizons 3 cards'],
            ['name' => 'Toploaders', 'type' => BinderType::Toploader, 'description' => 'High-value singles in toploaders'],
            ['name' => 'Holiday Gift Tin', 'type' => BinderType::Tin, 'description' => '2024 holiday promo tin'],
            ['name' => 'Misc Tokens', 'type' => BinderType::Other, 'description' => 'Token and emblem collection', 'custom_type' => 'Token Box'],
        ];

        $createdContainers = [];
        foreach ($containers as $index => $data) {
            $createdContainers[] = Container::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'type' => $data['type'],
                'description' => $data['description'],
                'custom_type' => $data['custom_type'] ?? null,
                'default_card_id' => $cardIds[$index] ?? null,
                'sort_order' => $index + 1,
            ]);
        }

        // Distribute 60 random cards across the containers.
        $randomCards = DefaultCard::inRandomOrder()->limit(60)->pluck('id');
        $languages = [CardLanguage::En, CardLanguage::De];

        foreach ($randomCards as $cardId) {
            $container = $createdContainers[array_rand($createdContainers)];

            CardStack::create([
                'user_id' => $user->id,
                'default_card_id' => $cardId,
                'container_id' => $container->id,
                'amount' => random_int(1, 4),
                'language' => $languages[array_rand($languages)],
            ]);
        }
    }
}
