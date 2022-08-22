<?php

namespace App\Controllers;

abstract class BaseController
{
    public function getClassName() {
        return get_called_class();
    }
}