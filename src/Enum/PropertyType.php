<?php

namespace App\Enum;

enum PropertyType: string
{
    case Apartment = 'apartment';
    case House = 'house';
    case Commercial = 'commercial';
    case Land = 'land';
    case Parking = 'parking';
}