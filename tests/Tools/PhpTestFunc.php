<?php
namespace tests\Tools;

use Aura\Session\Phpfunc;

class PhpTestFunc extends Phpfunc
{
    public function session_start()
    {
        return;
    }
}