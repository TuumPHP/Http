<?php
namespace Tuum\Respond\Controller;

use Negotiation\Accept;
use Negotiation\Negotiator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait PresentByContentTrait
{
    use ResponderHelperTrait;

    /**
     * list available acceptable mime types and methods to handle, such as;
     * [
     *    'mime-type' => 'method-name',
     *    'application/json' => 'json',
     *    'application/xml'  => 'xml',
     * ]
     *
     * @Override
     * @var string[]
     */
    protected $methodsList = [
    ];

    /**
     * @var string[]
     */
    private $defaultMethodList = [
        'text/html; charset=UTF-8' => 'html',
        'application/json' => 'json',
        'application/xml'  => 'xml',
    ];
    
    /**
     * @param ServerRequestInterface $request
     * @param array                  $data
     * @return ResponseInterface
     * @throws \ReflectionException
     */
    protected function _dispatch(ServerRequestInterface $request, array $data)
    {
        $this->setRequest($request);
        
        $negotiator = new Negotiator();
        $accepts = $request->getServerParams()['HTTP_ACCEPT'];
        $methods = $this->_findMethodList();
        
        /** @var Accept $bestMime */
        $bestMime = $negotiator->getBest($accepts, array_keys($methods));
        $mimeType = $bestMime->getValue();
        $execute  = $methods[$mimeType];
        
        /** @var ResponseInterface $response */
        $response = $this->$execute($data);
        return $response->withHeader('Content-Type', $mimeType);
    }

    /**
     * @return string[]
     * @throws \ReflectionException
     */
    private function _findMethodList()
    {
        if (!empty($this->methodsList)) {
            return $this->methodsList;
        }
        $ref  = new \ReflectionClass($this);
        $list = [];
        foreach($this->defaultMethodList as $mime => $method) {
            if ($ref->hasMethod($method)) {
                $list[$mime] = $method;
            }
        }
        return $list;
    }
}