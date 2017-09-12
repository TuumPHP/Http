<?php
namespace Tuum\Respond\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface PresenterInterface
 * 
 * creates a response for presentation view. 
 *
 * @package Tuum\Respond\Service
 */
interface PresenterInterface
{
    /**
     * returns a presentation response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response);
}