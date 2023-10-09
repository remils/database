<?php

declare(strict_types=1);

namespace Remils\Database\Enum;

enum ParameterType: int
{
    case INTEGER = 0;
    case FLOAT   = 1;
    case STRING  = 2;
    case BOOLEAN = 3;
    case BLOB    = 4;
    case NULL    = 5;
    case JSON    = 6;
}
