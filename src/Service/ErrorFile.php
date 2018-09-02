<?php
namespace Tuum\Respond\Service;

use Tuum\Respond\Interfaces\ErrorFileInterface;

class ErrorFile implements ErrorFileInterface
{
    /**
     * @var string     where all error files are located
     */
    public $default_path = 'errors';
    
    /**
     * @var string     default error file
     */
    public $default_error = 'error';

    /**
     * @var array
     */
    public $statusView = [
        401 => 'unauthorized',  // for login error.
        403 => 'forbidden',     // for CSRF token error.
        404 => 'notFound',      // for not found.
    ];

    /**
     * 
     */
    public function __construct()
    {
        $this->default_path = rtrim($this->default_path, '/');
    }

    /**
     * construct ErrorView object.
     * set $viewStream is to render template.
     * set $options as array of:
     *   'path'    : default path to error files
     *   'default' : default error template file name.
     *   'status'  : index of http code to file name (i.e. ['code' => 'file']).
     *   'files'   : index of ile name to http code(s) (i.e. ['file' => [405, 406]]
     *
     * @param array $options
     * @return static
     */
    public static function forge(
        array $options
    ) {
        $error = new static();
        $options += [
            'path' => null,
            'default' => null,
            'status'  => [],
            'files'   => [],
        ];
        $error->default_path = $options['path'] ?: $error->default_path;
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

        $file = isset($this->statusView[$status]) ?
            $this->statusView[$status] :
            $this->default_error;
        
        return $this->default_path . '/' . $file;
    }
}