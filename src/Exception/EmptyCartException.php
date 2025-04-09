<?php

namespace App\Exception;

class EmptyCartException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Ваш кошик порожній');
    }
} 