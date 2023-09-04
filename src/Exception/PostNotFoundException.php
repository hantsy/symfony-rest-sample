<?php

namespace App\Exception;

use RuntimeException;
use Symfony\Component\Uid\Uuid;

class PostNotFoundException extends RuntimeException
{

    public function __construct(Uuid $uuid)
    {
        parent::__construct("Post #" . $uuid . " was not found");
    }

}