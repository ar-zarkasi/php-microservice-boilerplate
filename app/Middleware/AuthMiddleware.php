<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\ErrorCode;
use App\Services\AuthServices;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;

/**
 * Authentication Middleware
 * Validates the Bearer token and authenticates the user
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected AuthServices $authServices,
        protected HttpResponse $response
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Get token from Authorization header
        $authorization = $request->getHeaderLine('Authorization');

        if (empty($authorization)) {
            return $this->response->json([
                'error' => 'Unauthorized',
                'code' => ErrorCode::UNAUTHORIZED_ERROR,
                'message' => 'Your Not Authorized to access this resource.',
                'data' => null,
            ])->withStatus(ErrorCode::UNAUTHORIZED_ERROR);
        }

        // Extract Bearer token
        if (!preg_match('/Bearer\s+(.*)$/i', $authorization, $matches)) {
            return $this->response->json([
                'error' => 'Unauthorized',
                'code' => ErrorCode::UNAUTHORIZED_ERROR,
                'message' => 'Invalid authorization',
                'data' => null,
            ])->withStatus(ErrorCode::UNAUTHORIZED_ERROR);
        }

        $token = $matches[1];

        // Validate token and authenticate user
        $result = $this->authServices->validateToken($token);

        if ($result['error']) {
            return $this->response->json($result)->withStatus(ErrorCode::FORBIDDEN_ERROR);
        }

        // Continue to the next middleware/controller
        return $handler->handle($request);
    }
}
