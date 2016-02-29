<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ErrorViewInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Responder\View;

class ErrorView implements ErrorViewInterface
{
    /**
     * @var View
     */
    private $view;

    /**
     * @var string
     */
    public $default_error = 'errors/error';

    /**
     * @var array
     */
    public $statusView = [
        401 => 'errors/unauthorized',  // for login error.
        403 => 'errors/forbidden',     // for CSRF token error.
        404 => 'errors/notFound',      // for not found.
    ];

    /**
     * @param View $viewStream
     */
    public function __construct(View $viewStream)
    {
        $this->view = $viewStream;
    }

    /**
     * construct ErrorView object.
     * set $viewStream is to render template.
     * set $options as array of:
     *   'default' : default error template file name.
     *   'status'  : index of http code to file name (i.e. ['code' => 'file']).
     *   'files'   : index of ile name to http code(s) (i.e. ['file' => [123, 234]]
     *
     * @param View  $view
     * @param array $options
     * @return static
     */
    public static function forge(
        View $view,
        array $options
    ) {
        $error = new static($view);
        $options += [
            'default' => null,
            'status'  => [],
            'files'   => [],
        ];
        $error->default_error = $options['default'];
        $error->statusView    = $options['status'];
        foreach ($options['files'] as $file => $codes) {
            foreach ((array)$codes as $code) {
                $error->statusView[$code] = $file;
            }
        }

        return $error;
    }

    /**
     * @param int $status
     * @return string
     */
    private function findViewFromStatus($status)
    {
        $status = (string)$status;

        return isset($this->statusView[$status]) ?
            $this->statusView[$status] :
            $this->default_error;
    }

    /**
     * create a response for error view.
     *
     * @param ServerRequestInterface  $request
     * @param ResponseInterface       $response
     * @param int                     $status
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $status, $viewData)
    {
        $file = $this->findViewFromStatus($status);

        $response = $this->view->withRequest($request, $response)->render($file, $viewData);
        if ($response instanceof ResponseInterface) {
            $response = $response->withStatus($status);
        }
        return $response;
    }
}