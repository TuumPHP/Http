<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\View;

trait DispatchByRoute
{
    /**
     * @return array
     */
    abstract protected function getRoutes();

    /**
     * @param $method
     * @param $params
     * @return mixed
     */
    abstract protected function dispatchMethod($method, $params);

    /**
     * @return View
     */
    abstract protected function view();

    /**
     * @return ServerRequestInterface
     */
    abstract protected function getRequest();

    /**
     * @return string
     */
    abstract protected function getPathInfo();

    /**
     * @param array  $routes
     * @param string $path
     * @param string $method
     * @return []
     */
    abstract function matchRoute(array $routes, $path, $method);

    /**
     * @param ServerRequestInterface $request
     * @return null|ResponseInterface
     */
    protected function dispatch(ServerRequestInterface $request)
    {
        $method = $request->getMethod();
        $path   = $this->getPathInfo();
        if (strtoupper($method) === 'OPTIONS') {
            return $this->onOptions($path);
        }

        return $this->dispatchRoute($path, $method);
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
        return $this->view()->asResponse('', 200, ['Allow' => $list]);
    }

    /**
     * @param string  $path
     * @param string  $method
     * @return ResponseInterface|null
     */
    private function dispatchRoute($path, $method)
    {
        $routes = $this->getRoutes();
        foreach ($routes as $pattern => $dispatch) {
            $params = Matcher::verify($pattern, $path, $method);
            if (!empty($params)) {
                $params += $this->getRequest()->getQueryParams() ?: [];
                return $this->dispatchMethod($dispatch, $params);
            }
        }
        return null;
    }
}