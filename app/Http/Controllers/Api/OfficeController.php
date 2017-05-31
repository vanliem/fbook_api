<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\OfficeRepository;

class OfficeController extends ApiController
{
    protected $officeRepository;

    public function __construct(OfficeRepository $officeRepository)
    {
        parent::__construct();
        $this->officeRepository = $officeRepository;
    }

    public function index()
    {
        return $this->getData(function() {
            $this->compacts['items'] = $this->officeRepository->getData();
        });
    }
}
