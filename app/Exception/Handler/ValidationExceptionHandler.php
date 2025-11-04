<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        /** @var ValidationException $throwable */
        $errors = $throwable->validator->errors();

        // Get the first error message
        $firstError = $errors->first();

        $data = [
            'code' => ErrorCode::VALIDATION_ERROR,
            'message' => $firstError,
            'data' => [
                'errors' => $errors->toArray(),
            ],
        ];

        return $response
            ->withStatus(422)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream(json_encode($data)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
