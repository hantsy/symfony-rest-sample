<?php

namespace App\Annotation;

use Attribute;
use Symfony\Component\Routing\Annotation\Route;

#[Attribute]
class Head extends Route
{
    public function getMethods()
    {
       return ['HEAD'];
    }

}