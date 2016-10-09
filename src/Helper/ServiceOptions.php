<?php
namespace Tuum\Respond\Helper;

class ServiceOptions
{
    const RENDERER_PLATES = 'plates';
    const RENDERER_TWIG   = 'twig';
    const RENDERER_TUUM   = 'tuum';
    
    /**
     * @var array
     */
    public $default_options
        = [
            'app-name'       => 'tuum-app',
            // rendering options
            'template-path'  => null,
            'content-file'   => 'layouts/contents',
            // set up renderer
            'renderer'       => 'plates',   // set renderer type: plates, twig, or tuum.
            'renderer-options' => [
                'options'  => [],
                'callback' => null,
            ],
            // set up error files
            'error-files'    => [
                'default' => 'errors/error',
                'status'  => [
                    404 => 'errors/notFound',      // for not found.
                ],
            ],
        ];

    /**
     * ServiceOptions constructor.
     *
     * @param string $templatePath
     * @param string $appName
     */
    public function __construct($templatePath, $appName = 'tuum-app')
    {
        $this->set('template-path', $templatePath);
        $this->set('app-name', $appName);
    }

    /**
     * @param string $templatePath
     * @param string $appName
     * @return ServiceOptions
     */
    public static function forge($templatePath, $appName = 'tuum-app')
    {
        return new self($templatePath, $appName);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->default_options;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->default_options[$name] = $value;
        return $this;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setContentsFile($file)
    {
        $this->default_options['content-file'] = $file;
        return $this;
    }

    /**
     * @param string        $renderer
     * @param array         $options
     * @param null|callable $callback
     * @return $this
     */
    public function setRenderer($renderer, $options = [], $callback = null)
    {
        $this->set('renderer', $renderer);
        $this->default_options['renderer-options'] = [
            'options'  => $options,
            'callback' => $callback,
        ];
        return $this;
    }

    /**
     * @param string $file
     * @param int    $status
     * @return $this
     * @internal param int $status2
     * @internal param int $status3
     * @internal param int $status4
     */
    public function setErrorFiles($file, $status)
    {
        $status = func_get_args();
        array_shift($status);
        foreach($status as $s) {
            $this->default_options['error-files']['status'][$s] = $file;
        }
        return $this;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setDefaultError($file)
    {
        $this->default_options['error-files']['default'] = $file;
        return $this;
    }
}