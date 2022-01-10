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
use catchAdmin\basisinfo\model\EquipmentClass;
use catchAdmin\basisinfo\request\SupplierLicenseRequest;
use catcher\base\CatchController;
use catchAdmin\basisinfo\model\SupplierLicense;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;

/**
 * Class UploadFile
 * @package catchAdmin\common\controller
 */
class Suppliers extends CatchController
{

    public $supplier;
    public $equipmentClassModel;

    public function __construct(
        SupplierLicense $supplier,
        EquipmentClass $equipmentClassModel
    )
    {
        $this->supplier = $supplier;
        $this->equipmentClassModel = $equipmentClassModel;
    }

    /**
     * @return \think\response\Json
     * @author xiejiaqing
     */
    public function index()
    {
        return CatchResponse::paginate($this->supplier->getList());
    }

    /**
     * 保存
     *
     * @param Request $request
     * @return \think\response\Json
     * @author xiejiaqing
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
     * 更新
     *
     * @param $id
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xiejiaqing
     */
    public function update($id, Request $request)
    {
        $type = $request->param('suppliers_type') ?? "";
        if (empty($type)) {
            return CatchResponse::fail("请求类型缺失");
        }
        $result = $this->{$type}($request->param());
        if ($result) {
            return CatchResponse::success();
        }
        return CatchResponse::fail();
    }

    /**
     * 新增
     *
     * @param array $params
     * @return bool
     * @author xiejiaqing
     */
    public function businessLicenseCall(array $params)
    {
        $params['business_date_long'] = $params['business_date_long'] ? 1 : 0;
        $params['data_maintenance'] = implode(",", $params['data_maintenance']);
        $params['registration_date'] = strtotime($params['registration_date']);
        $params['business_start_date'] = strtotime($params['business_start_date']);
        $params['establish_date'] = strtotime($params['establish_date']);
        unset($params['suppliers_type']);

        $where = $this->supplier->where("unified_code", $params['unified_code']);
        if (isset($params['id']) && !empty($params['id'])) {
            $where = $where->where("id", "<>", $params['id']);
        }
        $this->validator(SupplierLicenseRequest::class, $params);
        $data = $where->find();
        if (!empty($data)) {
            throw new BusinessException("统一社会信用码已存在");
        }
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->supplier->updateBy($params['id'], $params);
        } else {
            return $this->supplier->storeBy($params);
        }
    }

    /**
     * 通过请求的接口返回显示的内容
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xiejiaqing
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
        // 营业执照信息
        $businessData = [];
        if ($id != 0) {
            $data = $this->supplier->find(['id' => $id]);
            if (!$data) {
                // 数据不存在
                return CatchResponse::fail("数据不存在");
            }
            $data['data_maintenance'] = explode(",", $data['data_maintenance']);
            $mustNeed = $this->getComponentData($data['data_maintenance']);
            $map = array_merge($map, $mustNeed);
            $businessData['businessLicenseData'] = $data;
        }
        return CatchResponse::success([
            'componentData' => $map,
            'businessData' => $businessData,
        ]);
    }

    /**
     * 获取数据
     *
     * @param $dataMaintenance
     * @return array[]
     * @author xiejiaqing
     */
    private function getComponentData($dataMaintenance): array
    {
        $mustNeed = [];
        foreach ($dataMaintenance as $value) {
            if ($value == 1) {
                array_push($mustNeed, [
                    "id" => 2,
                    "name" => "经营许可证",
                    "component" => "operating_license",
                ]);
            } else {
                array_push($mustNeed, [
                    "id" => 3,
                    "name" => "经营备案凭证",
                    "component" => "registration_license",
                ]);
            }
        }
        array_push($mustNeed,
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
        );
        return $mustNeed;
    }

    /**
     * 获取数据
     *
     * @param array $ids
     * @return array
     * @author xiejiaqing
     */
    private function getBusinessLicenseData(array $ids): array
    {
        return [];
    }

    /**
     * 获取医疗器械分类目录
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     *@author xiejiaqing
     */
    public function getBusinessScope(): \think\response\Json
    {
        return CatchResponse::success($this->equipmentClassModel->select()->toArray());
    }

}