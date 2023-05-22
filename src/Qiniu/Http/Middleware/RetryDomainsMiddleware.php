<?php
namespace Qiniu\Http\Middleware;

use Qiniu\Http\Request;
use Qiniu\Http\Response;

class RetryDomainsMiddleware implements Middleware
{
    /**
     * @var array<string> backup domains.
     */
    private $backupDomains;

    /**
     * @var numeric max retry times for each backup domains.
     */
    private $maxRetryTimes;

    /**
     * @param array<string> $backupDomains
     * @param numeric $maxRetryTimes
     */
    public function __construct($backupDomains, $maxRetryTimes = 2)
    {
        $this->backupDomains = $backupDomains;
        $this->maxRetryTimes = $maxRetryTimes;
    }

    /**
     * @param Request $request
     * @param callable(Request): Response $next
     * @return Response
     */
    public function send($request, $next)
    {
        $response = null;
        $urlComponents = parse_url($request->url);

        foreach (array_merge(array($urlComponents["host"]), $this->backupDomains) as $backupDomain) {
            $urlComponents["host"] = $backupDomain;
            $request->url = \Qiniu\unparse_url($urlComponents);
            $retriedTimes = 0;

            while ($retriedTimes < $this->maxRetryTimes) {
                $response = $next($request);

                $retriedTimes += 1;

                if ($response->ok()) {
                    return $response;
                }
            }
        }

        if (!$response) {
            $response = $next($request);
        }

        return $response;
    }
}
