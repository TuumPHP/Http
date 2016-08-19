<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Interfaces\RenderErrorInterface;
use Tuum\Respond\Interfaces\RendererInterface;

class RenderError implements RenderErrorInterface
{
    /**
     * @var RendererInterface
     */
    private $renderer;

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
     * @param RendererInterface $viewStream
     */
    public function __construct($viewStream)
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
     * @param RendererInterface  $view
     * @param array $options
     * @return static
     */
    public static function forge(
        $view,
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
     * @param int   $status
     * @param array $data
     * @param array $helper
     * @return ResponseInterface
     */
    public function __invoke($status, array $data, array $helper = [])
    {
        $file = $this->findViewFromStatus($status);

        return $this->renderer->__invoke($file, $data, $helper);
    }
}