<?php
namespace App\Demo;

use App\Demo\Handler\CatchThrows;
use App\Demo\Handler\CsRfToken;
use App\Demo\Handler\Dispatcher;
use App\Demo\Handler\NotFound;

class Middleware
{
    private $chains  = [];
    
    public function __construct()
    {
        $this->chains = [
            CatchThrows::class,
            CsRfToken::class,
            Dispatcher::class,
            NotFound::class,
        ];
    }

    public function get(): array
    {
        return $this->chains;
    }

}