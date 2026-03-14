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
     * Extract the first art crop URI from a Scryfall card object.
     *
     * Returns the card-level art crop if available, otherwise the first
     * face's art crop for multi-faced cards (e.g. transform, modal DFC).
     * Returns null if no art crop is available.
     *
     * @param  array  $card  A single card object from the Scryfall bulk JSON.
     * @return string|null
     */
    public function getArtCrop(array $card): ?string
    {
        if (array_key_exists('image_uris', $card) && array_key_exists('art_crop', $card['image_uris'])) {
            return $card['image_uris']['art_crop'];
        }
        if (array_key_exists('card_faces', $card) && count($card['card_faces']) > 1) {
            foreach ($card['card_faces'] as $face) {
                if (array_key_exists('image_uris', $face) && array_key_exists('art_crop', $face['image_uris'])) {
                    return $face['image_uris']['art_crop'];
                }
            }
        }
        return null;
    }

    /**
     * Extract the Unix timestamp from a Scryfall image URL query string.
     *
     * Scryfall appends a cache-busting timestamp to image URLs, e.g.
     *   https://cards.scryfall.io/art_crop/front/a/b/uuid.jpg?1709234567
     * Returns null if no query string / timestamp is present.
     *
     * @param  string  $url  A Scryfall image URL.
     * @return string|null   The timestamp portion, or null.
     */
    public function parseTimestamp(string $url): ?string
    {
        $query = parse_url($url, PHP_URL_QUERY);
        return $query ?: null;
    }

    /**
     * Build the local filename for a cached art crop image.
     *
     * Embeds the Scryfall timestamp in the filename so that a filesystem
     * check alone can determine whether the cached version is current.
     *
     * Format: {uuid}--{timestamp}.jpg  (or {uuid}.jpg if no timestamp)
     *
     * @param  string       $uuid       The card UUID.
     * @param  string|null  $timestamp  The timestamp from the Scryfall URL.
     * @return string  e.g. "abcdef-1234--1709234567.jpg"
     */
    public function buildArtCropFilename(string $uuid, ?string $timestamp): string
    {
        if ($timestamp !== null) {
            return "$uuid--$timestamp.jpg";
        }
        return "$uuid.jpg";
    }

    /**
     * Build the full local path for a cached art crop image.
     *
     * @param  string       $setCode    The set code (e.g. "lea", "mh3").
     * @param  string       $uuid       The card UUID.
     * @param  string|null  $timestamp  The timestamp from the Scryfall URL.
     * @return string  e.g. "art-crops/lea/abcdef-1234--1709234567.jpg"
     */
    public function buildArtCropPath(string $setCode, string $uuid, ?string $timestamp): string
    {
        return "art-crops/$setCode/" . $this->buildArtCropFilename($uuid, $timestamp);
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
