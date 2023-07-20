<?php
namespace Qiniu\Http\Middleware;

use Qiniu\Auth;
use Qiniu\Http\Request;
use Qiniu\Http\Response;

class AuthMiddleware implements Middleware
{
    /**
     * @var Auth auth object
     */
    private $auth;

    /**
     * @param Auth $auth
     */
    public function __construct($auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @param callable(Request): Response $next
     * @return Response
     */
    public function send($request, $next)
    {
        $authHeaders = $this->auth->authorizationV2(
            $request->url,
            $request->method,
            $request->body,
            $request->headers["Content-Type"]
        );

        foreach ($authHeaders as $fieldName => $fieldValue) {
            $request->headers[$fieldName] = $fieldValue;
        }

        return $next($request);
    }
}
