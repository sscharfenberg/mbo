<?php

namespace App\Services;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    /**
     * Generate an inline-embeddable SVG QR code for the given URL.
     *
     * Uses bacon/bacon-qr-code (same approach as Fortify's 2FA QR).
     * Strips the XML declaration so the SVG can be embedded via v-html.
     */
    public static function generateSvg(string $url, int $size = 256): string
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle($size, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(0, 0, 0))),
                new SvgImageBackEnd
            )
        ))->writeString($url);

        // Strip XML declaration so the SVG can be embedded via v-html.
        return trim(substr($svg, strpos($svg, "\n") + 1));
    }
}
