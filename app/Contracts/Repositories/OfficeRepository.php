<?php

namespace App\Contracts\Repositories;

interface OfficeRepository extends AbstractRepository
{
    public function getData(array $data = [], $with = [], $dataSelect = ['*']);
}
