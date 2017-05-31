<?php

namespace App\Contracts\Repositories;

interface CategoryRepository extends AbstractRepository
{
    public function getData(array $data = [], $with = [], $dataSelect = ['*']);
}
