<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Interfaces\ErrorFileInterface;
use Tuum\Respond\Responder\View;

class ErrorFileView implements ErrorFileInterface
{
    /**
     * @var View
     */
    private $renderer;

    /**
     * @var string
     */
    public $default_error = 'errors/error';

    public $request;
    
    public $response;
    
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
        $this->renderer = $viewStream;
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
        $error->default_error = $options['default'] ?: $error->default_error;
        foreach($options['status'] as $status => $file) {
            $error->statusView['status'][$status] = $file;
        }
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
    public function find($status)
    {
        $status = (string)$status;

        return isset($this->statusView[$status]) ?
            $this->statusView[$status] :
            $this->default_error;
    }
}