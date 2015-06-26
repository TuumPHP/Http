<?php
namespace tests\Http;

use tests\Tools\PhpTestFunc;
use Tuum\Respond\Service\SessionStorage;

trait TesterTrait
{
    /**
     * @param SessionStorage $session_factory
     */
    function setPhpTestFunc($session_factory)
    {
        $session = new \ReflectionProperty($session_factory, 'session');
        $session->setAccessible(true);
        $session = $session->getValue($session_factory);
        $phpFunc = new \ReflectionProperty($session, 'phpfunc');
        $phpFunc->setAccessible(true);
        $phpFunc->setValue($session, new PhpTestFunc());
    }

    /**
     * @param SessionStorage $session_factory
     */
    function moveFlash($session_factory)
    {
        $session = new \ReflectionProperty($session_factory, 'session');
        $session->setAccessible(true);
        $session = $session->getValue($session_factory);
        $moveFlash = new \ReflectionMethod($session, 'moveFlash');
        $moveFlash->setAccessible(true);
        $moveFlash->invoke($session);
    }

}