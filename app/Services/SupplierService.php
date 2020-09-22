<?php

namespace App\Service;

use App\Repositories\Supplier\SupplierRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SupplierService
{
    private $supplier;

    public function __construct(SupplierRepositoryInterface $supplier)
    {
        $this->supplier = $supplier;
    }
    public function useSuppliers()
    {
        $user_id = Auth::id();
        $result = $this->supplier->getUseSuppliers($user_id);
        return $result;
    }
}
