<?php
use Psr\Http\Message\ServerRequestInterface;

class AuthAPIMiddleware
{
    public function handle(ServerRequestInterface $request, Closure $next)
    {
        if (!Auth::check()) {
            // Call the next middleware/controller
            return $next($request);
        }

        return json_encode([
            'status' => "error",
            'message' => "Bạn đã đăng nhập vui lòng tải lại trang",
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    }
}
