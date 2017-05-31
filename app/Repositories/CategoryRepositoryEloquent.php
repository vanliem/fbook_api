<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoryRepository;
use App\Eloquent\Category;

class CategoryRepositoryEloquent extends AbstractRepositoryEloquent implements CategoryRepository
{
    public function model()
    {
        return new Category;
    }

    public function getData(array $data = [], $with = [], $dataSelect = ['*'])
    {
        $categories = $this->model()
            ->select($dataSelect)
            ->with($with)
            ->orderBy('created_at')
            ->get();

        return $categories;
    }
}
