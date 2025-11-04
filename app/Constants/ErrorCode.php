<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

#[Constants]
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("Server Error！")
     */
    public const SERVER_ERROR = 500;
    public const VALIDATION_ERROR = 422;
    public const NOT_FOUND_ERROR = 404;
    public const UNAUTHORIZED_ERROR = 401;
    public const FORBIDDEN_ERROR = 403;
    public const CONFLICT_ERROR = 409;
    public const TOO_MANY_REQUESTS_ERROR = 429;
    public const SERVICE_UNAVAILABLE_ERROR = 503;
    public const GATEWAY_TIMEOUT_ERROR = 504;
}
