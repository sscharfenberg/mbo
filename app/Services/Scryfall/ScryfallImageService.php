<?php

namespace App\Services\Scryfall;

class ScryfallImageService
{

    /**
     * Preferred image formats in descending priority order.
     *
     * The first matching format found on a card is used.
     *
     * @var string[]
     */
    protected array $imageFormats = ["large", "normal", "small", "png"];

    /**
     * Extract image URIs from a Scryfall card object.
     *
     * Checks the card-level image_uris first, then falls back to
     * per-face image_uris for multi-faced cards (e.g. transform, modal DFC).
     * Returns the highest-priority format available per $imageFormats.
     *
     * @param  array  $card  A single card object from the Scryfall bulk JSON.
     * @return array<string>  Zero or more image URIs.
     */
    public function getImageUris(array $card): array
    {
        $uris = [];
        if (array_key_exists('image_uris', $card) && count($card['image_uris']) > 0) {
            $uris[] = $this->getCardFaceImageUri($card);
        } elseif (array_key_exists('card_faces', $card) && count($card['card_faces']) > 1) {
            foreach ($card['card_faces'] as $face) {
                if (
                    array_key_exists('image_uris', $face)
                    && count($face['image_uris']) > 0
                    && strlen($this->getCardFaceImageUri($face)) > 0
                ) {
                    $uris[] = $this->getCardFaceImageUri($face);
                }
            }
        }
        return $uris;
    }

    /**
     * Extract art crop URIs from a Scryfall card object.
     *
     * Returns a single art crop for single-faced cards, or one per face
     * for multi-faced cards (e.g. transform, modal DFC). Returns an empty
     * array if no art crops are available.
     *
     * @param  array  $card  A single card object from the Scryfall bulk JSON.
     * @return array<string>  Zero, one, or two art crop URIs.
     */
    public function getArtCrops(array $card): array
    {
        $crops = [];
        if (array_key_exists('image_uris', $card) && array_key_exists('art_crop', $card['image_uris'])) {
            $crops[] = $card['image_uris']['art_crop'];
        } elseif (array_key_exists('card_faces', $card) && count($card['card_faces']) > 1) {
            foreach ($card['card_faces'] as $face) {
                if (array_key_exists('image_uris', $face) && array_key_exists('art_crop', $face['image_uris'])) {
                    $crops[] = $face['image_uris']['art_crop'];
                }
            }
        }
        return $crops;
    }

    /**
     * Resolve the best image URI for a single card face.
     *
     * Iterates through $imageFormats in priority order and returns
     * the first match. Returns an empty string if none are available.
     *
     * @param  array  $face  A card or card_face object from the Scryfall bulk JSON.
     * @return string
     */
    private function getCardFaceImageUri(array $face): string
    {
        foreach ($this->imageFormats as $format) {
            if (array_key_exists($format, $face['image_uris'])) {
                return $face['image_uris'][$format];
            }
        }
        return "";
    }

}
