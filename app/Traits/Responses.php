<?php
namespace App\Traits;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

trait Responses
{
    public function __construct(protected ResponseInterface $response) {
    }
    
    public function send($data = null, string $message = 'success', int $code = 200): Psr7ResponseInterface
    {
        return $this->response
        ->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ])
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($code);
    }

    public function download(string $fullpath, string $filename = null): Psr7ResponseInterface
    {
        return $this->response->download($fullpath, $filename);
    }
}