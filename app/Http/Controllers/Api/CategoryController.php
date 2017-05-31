<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\CategoryRepository;

class CategoryController extends ApiController
{
    public function __construct(CategoryRepository $repository)
    {
        parent::__construct($repository);
    }

    public function index()
    {
        return $this->getData(function() {
            $this->compacts['items'] = $this->repository->getData();
        });
    }
}
