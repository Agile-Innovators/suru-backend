<?php
namespace App\Http\Middleware;

use Closure;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            // Verifica el token JWT
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                throw new UnauthorizedHttpException('JWT', 'User not found');
            }
        } catch (JWTException $e) {
            // Si no se puede obtener el token
            return response()->json(['message' => 'Token is invalid or expired'], 401);
        }

        // Almacena el usuario en el request
        $request->auth = $user;

        return $next($request);
    }
}
