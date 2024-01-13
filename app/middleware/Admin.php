<?php
use MiladRahimi\PhpRouter\Router;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\HtmlResponse;

class Admin
{
    public function handle(ServerRequestInterface $request, Closure $next)
    {
        if (Auth::check()):            
            if(Auth::user()->level == 4):
               return $next($request);
            endif;
        endif;

        return new HtmlResponse('Đi lạc à chú em!', 404);
    }
}