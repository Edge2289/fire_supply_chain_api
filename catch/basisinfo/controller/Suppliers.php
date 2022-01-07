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
use catcher\CatchResponse;

/**
 * Class UploadFile
 * @package catchAdmin\common\controller
 */
class Suppliers extends CatchController
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

    /**
     * 通过请求的接口返回显示的内容
     *
     * @author xiejiaqing
     * @return \think\response\Json
     */
    public function changeSuppliersSetting()
    {
        return CatchResponse::success([
            [
                "id" => 1,
                "name" => "营业执照信息",
                "component" => "business_license",
            ],
            [
                "id" => 2,
                "name" => "经营许可证",
                "component" => "operating_license",
            ],
            [
                "id" => 3,
                "name" => "经营备案凭证",
                "component" => "registration_license",
            ],
            [
                "id" => 4,
                "name" => "补充信息",
                "component" => "supplementary",
            ],
            [
                "id" => 5,
                "name" => "资质与附件",
                "component" => "attachment",
            ]
        ]);
    }
}