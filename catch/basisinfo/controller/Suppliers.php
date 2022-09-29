<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/5
 * Time: 22:46
 */

namespace catchAdmin\basisinfo\controller;


use app\Request;
use catchAdmin\basisinfo\model\BusinessAttachment;
use catchAdmin\basisinfo\model\EquipmentClass;
use catchAdmin\basisinfo\model\OperatingLicense;
use catchAdmin\basisinfo\model\RegistrationLicense;
use catchAdmin\basisinfo\model\SuppleInfo;
use catchAdmin\basisinfo\request\AuditSuppliersRequest;
use catchAdmin\basisinfo\request\OperatingLicenseRequest;
use catchAdmin\basisinfo\request\RegistrationLicenseRequest;
use catchAdmin\basisinfo\request\SuppleInfoRequest;
use catchAdmin\basisinfo\request\SupplierLicenseRequest;
use catcher\base\CatchController;
use catchAdmin\basisinfo\model\SupplierLicense;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use fire\data\ChangeStatus;

/**
 * 供应商管理
 *
 * Class UploadFile
 * @package catchAdmin\common\controller
 */
class Suppliers extends CatchController
{

    public $supplier;
    public $equipmentClassModel;
    public $operatingLicenseModel;
    public $registrationLicenseModel;
    public $suppleInfoModel;
    public $businessAttachmentModel;

    private $attr = [
        "business_license_url" => "营业执照",
        "production_license_url" => "医疗器械经营许可证/生产许可证",
        "record_certificate_url" => "第二类医疗器械经营备案凭证/生产备案凭证",
        "basic_deposit_account_url" => "基本存款账户信息",
        "person_authorization_url" => "法人委托授权书+身份证复印件",
        "out_invoice_url" => "开票资料",
        "system_survey_form_url" => "质量体系调查表",
        "annual_report_url" => "年度报告",
        "supplier_power_attorney_url" => "供应商授权书",
        "quality_assurance_url" => "质保协议",
        "after_sales_service_agreement_url" => "售后服务协议",
        "invoice_template_url" => "发票模板",
        "delivery_template_url" => "出库单模板"
    ];

    public function __construct(
        SupplierLicense     $supplier,
        EquipmentClass      $equipmentClassModel,
        OperatingLicense    $operatingLicenseModel,
        RegistrationLicense $registrationLicenseModel,
        SuppleInfo          $suppleInfoModel,
        BusinessAttachment  $businessAttachmentModel
    )
    {
        $this->supplier = $supplier;
        $this->equipmentClassModel = $equipmentClassModel;
        $this->operatingLicenseModel = $operatingLicenseModel;
        $this->registrationLicenseModel = $registrationLicenseModel;
        $this->suppleInfoModel = $suppleInfoModel;
        $this->businessAttachmentModel = $businessAttachmentModel;
    }

    /**
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function index()
    {
        $data = $this->supplier->getList();
        ChangeStatus::getInstance()->audit()->status([
            "未开启", "启用中"
        ])->handle($data);
        foreach ($data as &$datum) {
            ($datum['audit_status'] == 1) && $datum['audit_info'] = '通过';
        }
        return CatchResponse::paginate($data);
    }

    /**
     * 保存
     *
     * @param Request $request
     * @return \think\response\Json
     * @author 1131191695@qq.com
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
     * @author 1131191695@qq.com
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
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function delete()
    {
        return CatchResponse::fail("暂不开放删除供应商");
    }

    /**
     * 营业执照表
     *
     * @param array $params
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function businessLicenseCall(array $params)
    {
        if (!isset($params['company_type']) || empty($params['company_type'])) {
            $params['company_type'] = "经营企业";
        }
        $params['business_date_long'] = $params['business_date_long'] ? 1 : 0;
        $params['data_maintenance'] = implode(",", $params['data_maintenance']);
        $params['registration_date'] = isset($params['registration_date']) ? strtotime($params['registration_date']) : 0;
        $params['business_start_date'] = strtotime($params['business_start_date']);
        if (!empty($params['business_end_date'])) {
            $params['business_end_date'] = strtotime($params['business_end_date']);
        }
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
            $data = $this->supplier->where('id', $params['id'])->find();
            if (!$data) {
                throw new BusinessException("数据不存在");
            }
            return $this->supplier->updateBy($params['id'], $params);
        } else {
            return $this->supplier->storeBy($params);
        }
    }

    /**
     * 经营许可证
     *
     * @param array $params
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function operatingLicenseCall(array $params)
    {
        if (!empty($params['business_end_date'])) {
            $params['business_end_date'] = strtotime($params['business_end_date']);
        }
        $params['business_start_date'] = strtotime($params['business_start_date']);
        unset($params['suppliers_type']);
        $this->validator(OperatingLicenseRequest::class, $params);
        $params['equipment_class'] = implode(",", $params['equipment_class']);
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->operatingLicenseModel->updateBy($params['id'], $params);
        } else {
            return $this->operatingLicenseModel->storeBy($params);
        }
    }

    /**
     * 备案凭证
     *
     * @param array $params
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function registrationLicenseCall(array $params)
    {
        $params['registration_date'] = strtotime($params['registration_date']);
        unset($params['suppliers_type']);
        $params['equipment_class'] = implode(",", $params['equipment_class']);
        $this->validator(RegistrationLicenseRequest::class, $params);
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->registrationLicenseModel->updateBy($params['id'], $params);
        } else {
            return $this->registrationLicenseModel->storeBy($params);
        }
    }

    /**
     * 补充信息
     *
     * @param array $params
     * @return bool|int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function suppleInfoCall(array $params)
    {
        $params['license_end_date'] = strtotime($params['license_end_date']);
        $params['license_start_date'] = strtotime($params['license_start_date']);
        if (!empty($params['license_end_date'])) {
            $params['license_end_date'] = strtotime($params['license_end_date']);
        }
        $params['license_date_long'] = $params['license_date_long'] ? 1 : 0;
        unset($params['suppliers_type']);
        $this->validator(SuppleInfoRequest::class, $params);
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->suppleInfoModel->updateBy($params['id'], $params);
        } else {
            return $this->suppleInfoModel->insert($params);
        }
    }

    /**
     * @param array $params
     * @return bool|int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function businessAttachmentCall(array $params)
    {
        unset($params['suppliers_type']);
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->businessAttachmentModel->updateBy($params['id'], $params);
        } else {
            return $this->businessAttachmentModel->insert($params);
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
     * @author 1131191695@qq.com
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
            $this->getBusinessLicenseData($id, $businessData, array_column($map, 'id'));
        }
        return CatchResponse::success([
            'componentData' => $map,
            'businessData' => $businessData,
            'businessScope' => $this->getBusinessScope(),
        ]);
    }

    /**
     * 获取数据
     *
     * @param $dataMaintenance
     * @return array[]
     * @author 1131191695@qq.com
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
     * @param int $business_license_id
     * @param array $businessData
     * @param array $ids
     * @return void
     * @author 1131191695@qq.com
     */
    private function getBusinessLicenseData(int $business_license_id, array &$businessData, array $ids): void
    {
        /**
         * 2  经营许可证 OperatingLicense
         * 3  经营备案凭证 RegistrationLicense
         * 4  补充信息 SuppleInfo
         * 5  资质与附件 BusinessAttachment
         */
        $infoHandle = [
            2 => [
                'name' => 'operatingLicenseData',
                'model' => $this->operatingLicenseModel,
                'handle' => function ($data) {
                    $equipmentClass = [];
                    foreach (explode(",", $data['equipment_class']) as $equipment_class) {
                        $equipmentClass[] = (int)$equipment_class;
                    }
                    $data['equipment_class'] = $equipmentClass;
                    $data['operation_mode'] = (string)$data['operation_mode'];
                    return $data;
                }
            ],
            3 => [
                'name' => 'registrationLicenseData',
                'model' => $this->registrationLicenseModel,
                'handle' => function ($data) {
                    $equipmentClass = [];
                    foreach (explode(",", $data['equipment_class']) as $equipment_class) {
                        $equipmentClass[] = (int)$equipment_class;
                    }
                    $data['equipment_class'] = $equipmentClass;
                    $data['operation_mode'] = (string)$data['operation_mode'];
                    return $data;
                }
            ],
            4 => [
                'name' => 'suppleInfoData',
                'model' => $this->suppleInfoModel
            ],
            5 => [
                'name' => 'businessAttachmentData',
                'model' => $this->businessAttachmentModel,
            ],
        ];
        foreach ($ids as $id) {
            if (!isset($infoHandle[$id])) {
                continue;
            }
            $data = $infoHandle[$id]['model']->where("business_license_id", $business_license_id)->find();
            if (!empty($data)) {
                $data = $data->toArray();
                if (isset($infoHandle[$id]['handle'])) {
                    $data = $infoHandle[$id]['handle']($data);
                }
            }
            $businessData[$infoHandle[$id]['name']] = $data ?: "";
        }
        $businessData['businessAttachmentData'] = $this->getDefaultAtta($businessData['businessAttachmentData']);
    }

    /**
     * 获取默认资质附件
     *
     * @param $businessAttachmentData
     * @return array
     */
    public function getDefaultAtta($businessAttachmentData)
    {
        $map = [];
        foreach ($this->attr as $k => $value) {
            $url = $businessAttachmentData[$k] ?? '';
            $map[] = [
                'name' => $value,
                'key' => $k,
                'url' => $url
            ];
        }
        return $map;
    }

    /**
     * 获取医疗器械分类目录
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function getBusinessScope(): array
    {
        return $this->equipmentClassModel->select()->toArray();
    }

    /**
     * 审核供应商
     *
     * @param AuditSuppliersRequest $auditSuppliers
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function auditSuppliers(AuditSuppliersRequest $auditSuppliers)
    {
        $data = $auditSuppliers->param();
        $supplierData = $this->supplier->findBy($data['id']);
        if (empty($supplierData)) {
            throw new BusinessException("不存在供应商");
        }
        $b = $this->supplier->updateBy($data['id'], [
            'audit_status' => $data['audit_status'],
            'audit_info' => $data['audit_info'],
            'audit_user_id' => request()->user()->id,
            'audit_user_name' => request()->user()->username,
        ]);
        if ($b) {
            return CatchResponse::success();
        }
        return CatchResponse::fail("操作失败");
    }

    /**
     * 开启供应商
     *
     * @param Request $request
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function openSuppliers(Request $request)
    {
        $data = $request->param();
        if (!isset($data['id']) || empty($data['id'])) {
            throw new BusinessException("请选择供应商进行操作");
        }
        $supplierData = $this->supplier->findBy($data['id']);
        if (empty($supplierData)) {
            throw new BusinessException("不存在供应商");
        }
        // 开启的供应商必须要是审核过后的
        // 审核状态 {0:未审核,1:已审核,2:审核失败}
        if ($supplierData['audit_status'] != 1) {
            throw new BusinessException("审核状态不是已审核，无法开启");
        }
        if ($supplierData['status'] == 1) {
            throw new BusinessException("已开启");
        }
        // 开启
        $b = $this->supplier->updateBy($data['id'], [
            'status' => 1
        ]);
        if ($b) {
            return CatchResponse::success();
        }
        return CatchResponse::fail("操作失败");
    }

    /**
     * 关闭供应商状态
     *
     * @param Request $request
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function disabledSuppliers(Request $request)
    {
        $data = $request->param();
        if (!isset($data['id']) || empty($data['id'])) {
            throw new BusinessException("请选择供应商进行操作");
        }
        $supplierData = $this->supplier->findBy($data['id']);
        if (empty($supplierData)) {
            throw new BusinessException("不存在供应商");
        }
        if ($supplierData['status'] == 0) {
            throw new BusinessException("未开启无法停用");
        }
        // 开启
        $b = $this->supplier->updateBy($data['id'], [
            'status' => 0
        ]);
        if ($b) {
            return CatchResponse::success();
        }
        return CatchResponse::fail("操作失败");
    }

    /**
     * 上传附件调整
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function uploadAtt(Request $request)
    {
        $data = $request->param();
        $model = $this->businessAttachmentModel->where('business_license_id', $data['business_license_id'])->find();
        if ($model) {
            $this->businessAttachmentModel->updateBy($model['id'], [
                $data['nKey'] => $data['url']
            ]);
        } else {
            $this->businessAttachmentModel->insert([
                'business_license_id' => $data['business_license_id'],
                $data['nKey'] => $data['url']
            ]);
        }
        return CatchResponse::ok('文件上传成功');
    }
}