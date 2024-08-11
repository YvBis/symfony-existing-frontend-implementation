<?php

namespace App\Enum;

enum JwtAlgorithms: string
{
    case HS256 = 'HS256';
    case HS384 = 'HS384';
    case HS512 = 'HS512';
    case RSS256 = 'RSA256';
    case RSS512 = 'RSA512';
}
