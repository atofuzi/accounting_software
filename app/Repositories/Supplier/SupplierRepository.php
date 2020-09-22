<?php

namespace App\Repositories\Supplier;

use App\Models\Supplier;

class SupplierRepository implements SupplierRepositoryInterface
{
    protected $supplier;

    /**
     *@var Model $supplier 
     */

    public function __construct(Supplier $supplier)
    {
        $this->supplier = $supplier;
    }
    public function getUseSuppliers($id)
    {
        $result = $this->supplier
            ->select('id', 'supplier_name AS name')
            ->where('user_id', $id)
            ->get();

        return $result;
    }
}
