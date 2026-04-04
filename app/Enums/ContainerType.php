<?php

namespace App\Enums;

enum ContainerType: string
{
    case Binder = 'binder';
    case Deckbox = 'deckbox';
    case Display = 'display';
    case Box = 'box';
    case Other = 'other';
    case Cube = 'cube';
    case Tin = 'tin';
    case Toploader = 'Toploader';
}
