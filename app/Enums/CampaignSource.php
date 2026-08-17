<?php

declare(strict_types=1);

namespace App\Enums;

enum CampaignSource: string
{
    case Manual = 'manual';
    case Import = 'import';
    case Group = 'group';
}
