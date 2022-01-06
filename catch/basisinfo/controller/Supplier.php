<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/5
 * Time: 22:46
 */

namespace catchAdmin\basisinfo\controller;


use catcher\base\CatchController;
use catchAdmin\basisinfo\model\Supplier as SupplierModel;

/**
 * Class UploadFile
 * @package catchAdmin\common\controller
 */
class Supplier extends CatchController
{

    public $supplier;

    public function __construct(SupplierModel $supplier)
    {
        $this->supplier = $supplier;
    }

    public function index()
    {
//        $this->supplier->storeBy()
        return 131;
    }
}