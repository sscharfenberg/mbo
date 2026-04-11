<?php

namespace Tests\Unit\Services;

use App\Services\CardNameNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CardNameNormalizerTest extends TestCase
{
    /**
     * Edge cases for the normalization pipeline. Each row asserts that the
     * raw input (left) maps to the expected canonical form (right). Inputs
     * cover accents, smart quotes, hyphens, punctuation, Alchemy prefixes,
     * and whitespace collapsing.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function normalizationCases(): array
    {
        return [
            'simple name' => ['Sol Ring', 'sol ring'],
            'accent folding' => ['Jötun Grunt', 'jotun grunt'],
            'accent with acute' => ['Márton Stromgald', 'marton stromgald'],
            'ligature ae' => ['Ætherling', 'aetherling'],
            'straight apostrophe dropped' => ["Lim-Dûl's Vault", 'lim duls vault'],
            'smart apostrophe dropped' => ['Lim-Dûl’s Vault', 'lim duls vault'],
            'comma becomes space' => ['Mairsil, the Pretender', 'mairsil the pretender'],
            'colon becomes space' => ['Circle of Protection: Red', 'circle of protection red'],
            'comma in long name' => ['Urza, Lord High Artificer', 'urza lord high artificer'],
            'alchemy prefix kept' => ['A-Liliana of the Veil', 'a liliana of the veil'],
            'no punctuation' => ['Hazoret the Fervent', 'hazoret the fervent'],
            'whitespace collapsed' => ['  Sol    Ring  ', 'sol ring'],
            'exclamation marks' => ['Ach! Hans, Run!', 'ach hans run'],
            'empty string' => ['', ''],
            'whitespace only' => ['   ', ''],
        ];
    }

    #[Test]
    #[DataProvider('normalizationCases')]
    public function it_normalizes_input(string $input, string $expected): void
    {
        $this->assertSame($expected, CardNameNormalizer::normalize($input));
    }

    #[Test]
    public function it_is_idempotent(): void
    {
        $name = "Lim-Dûl's Vault";
        $once = CardNameNormalizer::normalize($name);
        $twice = CardNameNormalizer::normalize($once);

        $this->assertSame($once, $twice);
    }

    #[Test]
    public function it_produces_matching_output_for_equivalent_inputs(): void
    {
        // Straight and smart apostrophes should normalize identically, so
        // import (straight) and user query (might be either) always agree.
        $this->assertSame(
            CardNameNormalizer::normalize("Lim-Dûl's Vault"),
            CardNameNormalizer::normalize('Lim-Dûl’s Vault'),
        );
    }
}
