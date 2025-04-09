<?php

namespace App\Exception;

class UserNotFoundException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Користувача не знайдено. Будь ласка, увійдіть в систему.');
    }
} 