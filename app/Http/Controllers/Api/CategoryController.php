<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\CategoryRepository;

class CategoryController extends ApiController
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        parent::__construct();
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        return $this->getData(function() {
            $this->compacts['items'] = $this->categoryRepository->getData();
        });
    }
}
