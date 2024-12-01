<?php

declare(strict_types=1);

namespace App\Enum;

enum CsrfTokenConstant: string
{
    case API = 'api';
    case TOKEN_KEY = 'X-CSRFToken';
}
