<?php

namespace App\Repositories\Supplier;

interface SupplierRepositoryInterface
{
    /**
     * 取引先取得（売掛・買掛・小切手・手形）
     * @var integer $id
     * @return array
     */
    public function getUseSuppliers($id);
}
