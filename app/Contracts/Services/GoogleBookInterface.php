<?php

namespace App\Contracts\Services;

interface GoogleBookInterface
{
    public function search(array $params);
}
