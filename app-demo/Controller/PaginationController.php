<?php
namespace Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Pagination\Inputs;
use Tuum\Pagination\Pager;
use Tuum\Respond\Responder;

class PaginationController implements ControllerInterface
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var Pager
     */
    private $pagination;

    /**
     * PaginationController constructor.
     *
     * @param Responder  $responder
     * @param Pager $pagination
     */
    public function __construct(Responder $responder, Pager $pagination)
    {
        $this->responder = $responder;
        $this->pagination = $pagination;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $pager = $this->pagination->withRequest($request);
        $input = $pager->call(function($input) {
            return $input;
        });

        return $this->onGet($request, $response, $input);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param Inputs                 $input
     * @return ResponseInterface
     */
    private function onGet($request, $response, $input)
    {
        $total = $input->get('total', 500);
        $input->setTotal($total);

        return $this->responder->view($request, $response)
            ->render('pagination', [
                'input' => $input,
                'found' => $this->getFound($input),
            ]);
    }

    /**
     * @param Inputs $input
     * @return array
     */
    private function getFound($input)
    {
        $total = $input->get('total');
        $key   = $input->get('key', 'item');
        $love  = $input->get('love')? '♡　': ', ';
        $from  = $input->getOffset() + 1;
        $limit = $input->getLimit() + $input->getOffset();
        $limit = $limit < $total ? $limit: $total;

        $found = [];
        foreach(range($from, $limit) as $idx) {
            $found[] = sprintf("{$key}: %03d{$love}", $idx);
        }

        return $found;
    }
}