<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/5
 * Time: 22:46
 */

namespace catchAdmin\basisinfo\controller;


use app\Request;
use catcher\base\CatchController;
use catchAdmin\basisinfo\model\SupplierLicense;
use catcher\CatchResponse;

/**
 * Class UploadFile
 * @package catchAdmin\common\controller
 */
class Suppliers extends CatchController
{

    public $supplier;

    public function __construct(SupplierLicense $supplier)
    {
        $this->supplier = $supplier;
    }

    public function index()
    {
//        $this->supplier->storeBy()
        return 131;
    }

    /**
     * 保存
     *
     * @author xiejiaqing
     * @param Request $request
     */
    public function save(Request $request)
    {
        $type = $request->param('type') ?? "";
        if (empty($type)) {
            return CatchResponse::fail("请求类型缺失");
        }
        //
    }

    /**
     * 通过请求的接口返回显示的内容
     *
     * @author xiejiaqing
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function changeSuppliersSetting(Request $request)
    {
        $id = $request->param('id') ?? 0;
        $map = [
            [
                "id" => 1,
                "name" => "营业执照信息",
                "component" => "business_license",
            ]
        ];
        if ($id == 0) {
            return CatchResponse::success($map);
        }
        $data = $this->supplier->field(['id', 'data_maintenance'])->find(['id' => $id]);
        if (!$data) {
            // 数据不存在
            return CatchResponse::fail("数据不存在");
        }
        $data_maintenance = explode(",", $data['data_maintenance']);
        $mustNeed = [
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
        ];
        foreach ($data_maintenance as $value) {
            if ($value == 1) {
                array_push($map, [
                    "id" => 2,
                    "name" => "经营许可证",
                    "component" => "operating_license",
                ]);
            } else {
                array_push($map, [
                    "id" => 3,
                    "name" => "经营备案凭证",
                    "component" => "registration_license",
                ]);
            }
        }
        return CatchResponse::success(array_merge($map, $mustNeed));
    }
}