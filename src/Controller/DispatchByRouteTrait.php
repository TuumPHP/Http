<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\ReqAttr;

trait DispatchByRouteTrait
{
    use ControllerTrait;

    /**
     * return a hash of routes: [ pattern => handler, ],
     * where method = 'on'+handler
     * 
     * @return array
     */
    abstract protected function getRoutes();

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return null|ResponseInterface
     */
    protected function _dispatch($request, $response)
    {
        // dispatch by route
        $method         = ReqAttr::getMethod($request);
        $path           = ReqAttr::getPathInfo($request);
        if (strtoupper($method) === 'OPTIONS') {
            return $this->onOptions($path);
        }

        return $this->dispatchRoute($request, $path, $method);
    }

    /**
     * @param string $path
     * @return ResponseInterface
     */
    private function onOptions($path)
    {
        $routes  = $this->getRoutes();
        $options = ['OPTIONS', 'HEAD'];
        foreach ($routes as $pattern => $dispatch) {
            if ($params = Matcher::verify($pattern, $path, '*')) {
                if (isset($params['method']) && $params['method'] && $params['method'] !== '*') {
                    $options[] = strtoupper($params['method']);
                }
            }
        }
        $options = array_unique($options);
        sort($options);
        $list = implode(',', $options);

        return $this->getResponse()->withHeader('Allow', $list);
    }

    /**
     * @param ServerRequestInterface $request
     * @param string                 $path
     * @param string                 $method
     * @return ResponseInterface|null
     */
    private function dispatchRoute($request, $path, $method)
    {
        $routes = $this->getRoutes();
        foreach ($routes as $pattern => $dispatch) {
            $params = Matcher::verify($pattern, $path, $method);
            if (!empty($params)) {
                $params += $request->getQueryParams() ?: [];
                $method = 'on' . ucwords($dispatch);

                return $this->dispatchMethod($method, $params);
            }
        }

        return null;
    }
}
