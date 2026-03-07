<?php

namespace App\Enums;

enum BinderType: string
{
    case Binder  = 'binder';
    case Display = 'display';
    case Box     = 'box';
    case Other   = 'other';
}