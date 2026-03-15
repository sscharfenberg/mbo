<?php

namespace App\Enums;

enum FoilType: string
{
    case Foil       = 'foil';
    case Etched     = 'etched';
    case Surge      = 'surge';
    case Galaxy     = 'galaxy';
    case Gilded     = 'gilded';
    case Textured   = 'textured';
    case Serialized = 'serialized';
}