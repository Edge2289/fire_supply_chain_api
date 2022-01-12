<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 11:49
 */

namespace catchAdmin\basisinfo\controller;


use catcher\base\CatchController;
use catchAdmin\basisinfo\model\Customer as CustomerModel;
use catcher\base\CatchRequest;
use catcher\CatchResponse;

/**
 * 客户管理
 *
 * Class Customer
 * @package catchAdmin\basisinfo\controller
 */
class Customer extends CatchController
{
    public $customerModel;

    public function __construct(
        CustomerModel $customerModel
    )
    {
        $this->customerModel = $customerModel;
    }

    public function index()
    {
        return CatchResponse::paginate($this->customerModel->getList());
    }

    public function save()
    {

    }

    public function update()
    {

    }

    public function audit()
    {

    }

}