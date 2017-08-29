<?php
namespace Tuum\Respond\Service;

use Tuum\Respond\Interfaces\ErrorFileInterface;

class ErrorFileView implements ErrorFileInterface
{
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
     * 
     */
    public function __construct()
    {
    }

    /**
     * construct ErrorView object.
     * set $viewStream is to render template.
     * set $options as array of:
     *   'default' : default error template file name.
     *   'status'  : index of http code to file name (i.e. ['code' => 'file']).
     *   'files'   : index of ile name to http code(s) (i.e. ['file' => [123, 234]]
     *
     * @param array $options
     * @return static
     */
    public static function forge(
        array $options
    ) {
        $error = new static();
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