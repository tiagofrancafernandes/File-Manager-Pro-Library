<?php

namespace App\Enums;

enum StorageItemVisibilityEnum: int
{
    case PRIVATE = 10;
    case PUBLIC = 20;
    case SHARED = 30;
}
