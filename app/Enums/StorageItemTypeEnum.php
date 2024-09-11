<?php

namespace App\Enums;

enum StorageItemTypeEnum: int
{
    case FILE = 10;
    case DIR = 50;
    case LINK_TO_FILE = 70;
    case LINK_TO_DIR = 75;
}
