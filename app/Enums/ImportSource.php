<?php

namespace App\Enums;

enum ImportSource: string
{
    case Mbo = 'mbo';
    case Moxfield = 'moxfield';
    case Archidekt = 'archidekt';
}
