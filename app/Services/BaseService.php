<?php
declare(strict_types=1);

namespace App\Services;

use App\Traits\UsingCache;
use Hyperf\Cache\Cache;

class BaseService
{
    use UsingCache;
}