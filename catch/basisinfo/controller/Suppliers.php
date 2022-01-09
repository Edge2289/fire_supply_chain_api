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
use catchAdmin\basisinfo\request\SupplierLicenseRequest;
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

    /**
     * @author xiejiaqing
     * @return \think\response\Json
     */
    public function index()
    {
        return CatchResponse::paginate($this->supplier->getList());
    }

    /**
     * 保存
     *
     * @author xiejiaqing
     * @param Request $request
     */
    public function save(Request $request)
    {
        $type = $request->param('suppliers_type') ?? "";
        if (empty($type)) {
            return CatchResponse::fail("请求类型缺失");
        }
        $result = $this->{$type}($request->param());
        if ($result) {
            return CatchResponse::success([
                'id' => $result
            ]);
        }
        return CatchResponse::fail();
    }

    /**
     * 新增
     *
     * @author xiejiaqing
     * @param array $params
     * @return bool
     */
    public function businessLicenseCall(array $params)
    {
        $params['business_date_long'] = $params['business_date_long'] ? 1: 0;
        $params['data_maintenance'] = implode(",", $params['data_maintenance']);
        $params['registration_date'] = strtotime($params['registration_date']);
        $params['business_start_date'] = strtotime($params['business_start_date']);
        $params['establish_date'] = strtotime($params['establish_date']);
        unset($params['suppliers_type']);
        $this->validator(SupplierLicenseRequest::class, $params);
        return $this->supplier->storeBy($params);
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
        $businessData = [];
        if ($id != 0) {
            $data = $this->supplier->find(['id' => $id]);
            if (!$data) {
                // 数据不存在
                return CatchResponse::fail("数据不存在");
            }
            $data['data_maintenance'] = explode(",", $data['data_maintenance']);
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
            foreach ($data['data_maintenance'] as $value) {
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
            $map = array_merge($map, $mustNeed);
            $data['registration_date'] = date("Y-m-d", $data['registration_date']);
            $businessData['businessLicenseData'] = $data;
        }
        return CatchResponse::success([
            'componentData' => $map,
            'businessData' => $businessData
        ]);
    }

    /**
     * 获取数据
     *
     * @author xiejiaqing
     * @param array $ids
     * @return array
     */
    private function getBusinessLicenseData(array $ids): array
    {
        return [];
    }

}