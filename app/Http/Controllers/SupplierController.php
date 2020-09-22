<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\SupplierService;

class SupplierController extends Controller
{
    private $supplier_service = null;

    public function __construct(SupplierService $supplier_service)
    {
        $this->supplier_service = $supplier_service;
    }
    public function useSupplierLists()
    {
        $result = $this->supplier_service->useSuppliers();
        return $result;
    }
}
