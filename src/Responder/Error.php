<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Helper\ResponseHelper;
use Tuum\Respond\Interfaces\ErrorFileInterface;
use Tuum\Respond\Service\SessionStorage;

/**
 * Class Error
 *
 * @package Tuum\Respond\Responder
 *
 * @method ResponseInterface unauthorized()
 * @method ResponseInterface forbidden()
 * @method ResponseInterface notFound()
 */
class Error extends AbstractResponder
{
    const UNAUTHORIZED   = 401;
    const ACCESS_DENIED  = 403;
    const FILE_NOT_FOUND = 404;
    const INTERNAL_ERROR = 500;

    /**
     * @var View
     */
    private $view;
    
    /**
     * @var null|ErrorFileInterface
     */
    private $errorFile;

    /**
     * index of method name and associated http status code.
     *
     * @var array
     */
    public $methodStatus = [
        'unauthorized' => self::UNAUTHORIZED,
        'forbidden'    => self::ACCESS_DENIED,
        'notFound'     => self::FILE_NOT_FOUND,
    ];

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param ErrorFileInterface $errorFile
     * @param View               $view
     * @param SessionStorage     $session
     */
    public function __construct(ErrorFileInterface $errorFile, View $view, SessionStorage $session)
    {
        parent::__construct($session);
        $this->errorFile = $errorFile;
        $this->view = $view;
    }
    
    // +----------------------------------------------------------------------+
    //  methods for saving data for response.
    // +----------------------------------------------------------------------+
    /**
     * creates a generic response.
     *
     * @param string $input
     * @param int    $status
     * @param array  $header
     * @return ResponseInterface
     */
    public function respond($input, $status = self::INTERNAL_ERROR, array $header = [])
    {
        return ResponseHelper::fill($this->response, $input, $status, $header);
    }

    /**
     * @param int   $status
     * @param array $data
     * @return ResponseInterface
     */
    public function asJson($status, $data)
    {
        $stream = json_encode($data);

        return $this->respond($stream, $status, ['Content-Type' => 'application/json']);
    }

    /**
     * @param int                     $status
     * @param array $data
     * @return ResponseInterface
     */
    public function asView($status, $data = [])
    {
        $file = $this->errorFile->find($status);
        return $this->view->start($this->request, $this->response)->render($file, $data);
    }

    /**
     * @param string $method
     * @param array  $args
     * @return ResponseInterface
     */
    public function __call($method, $args)
    {
        if (isset($this->methodStatus[$method])) {
            $data = isset($args[0]) ? $args[0] : [];
            return $this->asView($this->methodStatus[$method], $data);
        }
        throw new \BadMethodCallException;
    }
}
