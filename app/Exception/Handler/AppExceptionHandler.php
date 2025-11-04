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

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use function Hyperf\Support\env;

class AppExceptionHandler extends ExceptionHandler
{
    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $environment = env('APP_ENV');
        $errorCode = ErrorCode::SERVER_ERROR;
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());
        $errors = [
            'message' => $environment === 'prod' ? 'Internal Server Error.' : $throwable->getMessage(),
            'code' => $errorCode,
            'data' => $environment === 'prod' ? null : $throwable->getTrace(),
        ];

        return $response
            ->withHeader('Server', 'Hyperf')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($errorCode)
            ->withBody(new SwooleStream(json_encode($errors)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
