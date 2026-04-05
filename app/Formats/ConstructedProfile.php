<?php

namespace App\Formats;

/**
 * Default profile for traditional 60-card constructed formats.
 *
 * Covers Standard, Pioneer, Modern, Legacy, Vintage, Pauper, Penny, Premodern,
 * Oldschool, Alchemy, Historic, Timeless, and Future. Pool differences between
 * these formats are expressed entirely through the `legalities` pivot table.
 */
class ConstructedProfile extends FormatProfile {}
