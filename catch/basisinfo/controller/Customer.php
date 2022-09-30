<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 11:49
 */

namespace catchAdmin\basisinfo\controller;


use app\Request;
use catchAdmin\basisinfo\model\CustomerAttachment;
use catchAdmin\basisinfo\model\CustomerInfo;
use catchAdmin\basisinfo\model\CustomerOperating;
use catchAdmin\basisinfo\model\CustomerPracticingLicense;
use catchAdmin\basisinfo\model\CustomerRegistration;
use catchAdmin\basisinfo\model\CustomerSuppleInfo;
use catchAdmin\basisinfo\model\EquipmentClass;
use catchAdmin\basisinfo\request\CustomerOperatingLicenseRequest;
use catchAdmin\basisinfo\request\CustomerRegistrationLicenseRequest;
use catchAdmin\basisinfo\request\CustomerSuppleInfoRequest;
use catchAdmin\basisinfo\request\CustomerSupplierLicenseRequest;
use catcher\base\CatchController;
use catchAdmin\basisinfo\model\CustomerLicense;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use fire\data\ChangeStatus;
use think\Db;

/**
 * 客户管理
 *
 * Class Customer
 * @package catchAdmin\basisinfo\controller
 */
class Customer extends CatchController
{
    public $customerLicenseModel;
    public $equipmentClassModel;
    public $operatingLicenseModel;
    public $registrationLicenseModel;
    public $customerInfoModel;
    public $businessAttachmentModel;
    public $customerSuppleInfo;

    private $attr = [
        "business_license_url" => "营业执照",
        "production_license_url" => "医疗器械经营许可证",
        "record_certificate_url" => "医疗器械经营备案凭证",
        "basic_deposit_account_url" => "基本存款账户信息",
        "person_authorization_url" => "法人委托授权书+身份证复印件",
        "out_invoice_url" => "开票资料",
        "system_survey_form_url" => "质量体系调查表",
        "record_form_seal_url" => "印章备案表",
        "annual_report_url" => "年度报告",
        "practicing_license_institution_url" => "医疗机构执业许可证",
    ];

    public function __construct(
        CustomerLicense      $customerLicenseModel,
        EquipmentClass       $equipmentClassModel,
        CustomerOperating    $customerOperating,
        CustomerRegistration $customerInfoModel,
        CustomerInfo         $customerInfo,
        CustomerAttachment   $attachment,
        CustomerSuppleInfo   $customerSuppleInfo
    )
    {
        $this->customerLicenseModel = $customerLicenseModel;
        $this->equipmentClassModel = $equipmentClassModel;
        $this->operatingLicenseModel = $customerOperating;
        $this->registrationLicenseModel = $customerInfoModel;
        $this->customerInfoModel = $customerInfo;
        $this->businessAttachmentModel = $attachment;
        $this->customerSuppleInfo = $customerSuppleInfo;
    }

    /**
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $data = $this->customerInfoModel->getList();
        $customer_type = [
            1 => '经营企业',
            2 => '医院(非公立)',
            3 => '医院(公立)',
        ];
        foreach ($data as &$datum) {
            if ($datum['customer_type'] == 1) {
                $datum['company_name'] = $datum['hasCustomerLicense']["company_name"] ?? '';
                $datum['effective_end_date'] = ($datum['hasCustomerLicense']['business_date_long'] ?? 0) == 1 ? "长期" : $datum['business_end_date'];
                $datum['legal_person'] = $datum['hasCustomerLicense']['legal_person'] ?? '';
                ($datum['audit_status'] == 1) && $datum['audit_info'] = '通过';
            }
            $datum['customer_type'] = $customer_type[$datum['customer_type']];
        }
        ChangeStatus::getInstance()->audit()->status([
            "未开启", "启用中"
        ])->handle($data);
        return CatchResponse::paginate($data);
    }

    /**
     * 保存
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function save(Request $request)
    {
        // 根据不同的类型添加不同的数据
        $type = $request->param("customer_type") ?: 3;
        $id = $request->param("customer_info_id");
        if (empty($type)) {
            return CatchResponse::fail("缺失客户类型");
        }
        try {
            $this->customerInfoModel->startTrans();
            if (!$id) {
                $id = $this->customerInfoModel->insertGetId(['customer_type' => $type]);
            } else {
                $type = $this->customerInfoModel->where('id', $id)->find()['customer_type'];
            }
            $map = $request->param();
            if ($type != 3) {
                // 经销商内容 只要不是医院内容的话 则从suppliers_type 获取变更内容
                $type = $request->param('suppliers_type') ?? "";
                if (empty($type)) {
                    return CatchResponse::fail("请求类型缺失");
                }
                $result = $this->{$type}(array_merge($map, [
                    "customer_info_id" => $id
                ]));
            } else {
                // 医院内容
                $map['effective_end_date'] = strtotime($map['effective_end_date']);
                $map['effective_start_date'] = strtotime($map['effective_start_date']);
                $map['certification_date'] = strtotime($map['certification_date']);
                $map['data_maintenance'] = implode(",", $map['data_maintenance']);
                unset($map['id']);
                $result = $this->customerInfoModel->updateBy($id, $map);
            }
            $this->customerInfoModel->commit();
        } catch (\Exception $exception) {
            $this->customerInfoModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
        if ($result) {
            return CatchResponse::success([
                'id' => $id
            ]);
        }
        return CatchResponse::fail();
    }

    /**
     * 客户更新
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
        // 根据不同的类型添加不同的数据
        $type = $request->param("customer_type") ?: 3;
        $id = $request->param("id");
        if (empty($type)) {
            return CatchResponse::fail("缺失客户类型");
        }
        $map = $request->param();
        if ($type != 3) {
            if (!$id) {
                return CatchResponse::fail("缺失客户id");
            }
            $data = $this->customerInfoModel->findBy($id);
            if (empty($data)) {
                return CatchResponse::fail("客户数据不存在");
            }
            // 医院内容
            $map['data_maintenance'] = implode(",", $map['data_maintenance']);
            $map['effective_end_date'] = strtotime($map['effective_end_date']);
            $map['effective_start_date'] = strtotime($map['effective_start_date']);
            $map['certification_date'] = strtotime($map['certification_date']);
            unset($map['id']);
            $result = $this->customerInfoModel->updateBy($id, $map);
        } else {
            // 经销商内容 只要不是医院内容的话 则从suppliers_type 获取变更内容
            $type = $request->param('suppliers_type') ?? "";
            if (empty($type)) {
                return CatchResponse::fail("请求类型缺失");
            }
            $result = $this->{$type}($map);
        }
        if ($result) {
            return CatchResponse::success([
                'id' => $id
            ]);
        }
        return CatchResponse::fail();
    }

    /**
     * 审核
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function audit(Request $request)
    {
        // 审核
        // 审核id、审核状态、审核信息
        $data = $request->param();
        $customerData = $this->customerInfoModel->findBy($data['id']);
        if (empty($customerData)) {
            throw new BusinessException("不存在客户");
        }
        if ($customerData['audit'] == 1) {
            return CatchResponse::fail("已审核");
        }
        $b = $this->customerInfoModel->updateBy($data['id'], [
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
     * 开启
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function open(Request $request)
    {
        $data = $request->param();
        if (!isset($data['id']) || empty($data['id'])) {
            throw new BusinessException("请选择客户进行操作");
        }
        $supplierData = $this->customerInfoModel->findBy($data['id']);
        if (empty($supplierData)) {
            throw new BusinessException("不存在客户");
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
        $b = $this->customerInfoModel->updateBy($data['id'], [
            'status' => 1
        ]);
        if ($b) {
            return CatchResponse::success();
        }
        return CatchResponse::fail("操作失败");
    }

    /**
     * 停用
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function disabled(Request $request)
    {
        $data = $request->param();
        if (!isset($data['id']) || empty($data['id'])) {
            throw new BusinessException("请选择客户进行操作");
        }
        $supplierData = $this->customerInfoModel->findBy($data['id']);
        if (empty($supplierData)) {
            throw new BusinessException("不存在客户");
        }
        if ($supplierData['status'] == 0) {
            throw new BusinessException("未开启无法停用");
        }
        // 开启
        $b = $this->customerInfoModel->updateBy($data['id'], [
            'status' => 0
        ]);
        if ($b) {
            return CatchResponse::success();
        }
        return CatchResponse::fail("操作失败");
    }
//    ----------------- 额外 --------------------

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
        $params['business_date_long'] = $params['business_date_long'] ? 1 : 0;
        $params['data_maintenance'] = implode(",", $params['data_maintenance']);
        $params['registration_date'] = isset($params['registration_date']) ? strtotime($params['registration_date']) : 0;
        $params['business_start_date'] = strtotime($params['business_start_date']);
        if (!empty($params['business_end_date'])) {
            $params['business_end_date'] = strtotime($params['business_end_date']);
        } else {
            $params['business_end_date'] = 0;
        }
        $params['establish_date'] = strtotime($params['establish_date']);
        unset($params['suppliers_type'], $params['customer_type']);

        $this->validator(CustomerSupplierLicenseRequest::class, $params);
        if (isset($params['id']) && !empty($params['id'])) {
            $model = $this->customerLicenseModel->where('customer_info_id', $params['customer_info_id'])->find();
            if (!$model) {
                throw new BusinessException("数据不存在");
            }
            unset($params['created_at']);
            return $model->update($params, ['customer_info_id' => $params['id']]);
        } else {
            return $this->customerLicenseModel->storeBy($params);
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
        $params['business_date_long'] = $params['business_date_long'] ? 1 : 0;
        if (!empty($params['business_end_date'])) {
            $params['business_end_date'] = strtotime($params['business_end_date']);
        }
        $params['business_start_date'] = strtotime($params['business_start_date']);
        unset($params['suppliers_type']);
        $this->validator(CustomerOperatingLicenseRequest::class, $params);
        $params['equipment_class'] = implode(",", $params['equipment_class']);
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->operatingLicenseModel->updateBy($params['id'], $params);
        } else {
            return $this->operatingLicenseModel->storeBy($params);
        }
    }

    /**
     * 医疗机构执业许可证
     *
     * @param array $params
     * @return bool|int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function practicingLicenseCall(array $params)
    {
        unset($params['suppliers_type']);
        $params['equipment_class'] = implode(",", $params['equipment_class']);
        if (isset($params['id']) && !empty($params['id'])) {
            unset($params['created_at']);
            $params['updated_at'] = time();
            return CustomerPracticingLicense::where('id', $params['id'])->update($params);
        } else {
            $params['created_at'] = time();
            unset($params['id']);
            return CustomerPracticingLicense::insertGetId($params);
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
        $this->validator(CustomerRegistrationLicenseRequest::class, $params);
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
        $params['license_end_date'] = empty($params['license_end_date']) ? 0 : strtotime($params['license_end_date']);
        $params['license_start_date'] = strtotime($params['license_start_date']);
        if (!empty($params['license_end_date'])) {
            $params['license_end_date'] = strtotime($params['license_end_date']);
        }
        $params['license_date_long'] = $params['license_date_long'] ? 1 : 0;
        unset($params['suppliers_type']);
        $this->validator(CustomerSuppleInfoRequest::class, $params);
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->customerSuppleInfo->updateBy($params['id'], $params);
        } else {
            return $this->customerSuppleInfo->insert($params);
        }
    }

    /**
     * 资质与附件
     *
     * @param array $params
     * @return bool|int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function businessAttachmentCall(array $params)
    {
        $map = [];
        foreach ($params as $key => $param) {
            if (strpos($key, "check") === false) {
                $map[$key] = $param;
                continue;
            }
            $map[$key] = empty($param) ? 0 : $param[0];
        }
        unset($map['suppliers_type']);
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->businessAttachmentModel->updateBy($params['id'], $map);
        } else {
            return $this->businessAttachmentModel->insert($map);
        }
    }
//    ----------------- help --------------------

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
    public function changeCustomerSetting(Request $request, $customerId = 0)
    {
        $id = $customerId == 0 ? ($request->param('id') ?? 0) : $customerId;
        $map = [
            [
                "id" => 1,
                "name" => "客户信息",
                "component" => "business_license",
            ]
        ];
        // 营业执照信息
        $businessData = [];
        if ($id != 0) {
            $data = $this->customerInfoModel->find(['id' => $id]);
            if (!$data) {
                // 数据不存在
                return CatchResponse::fail("数据不存在");
            }
            if ($data['customer_type'] != 3) {
                $dataLicense = $this->customerLicenseModel->where("customer_info_id", $id)->find();
                if (empty($dataLicense)) {
                    $dataLicense['data_maintenance'] = [];
                    $businessData['businessLicenseData'] = [];
                } else {
                    $dataLicense['data_maintenance'] = explode(",", $dataLicense['data_maintenance']);
                    $mustNeed = $this->getComponentData($dataLicense['data_maintenance'], $data['customer_type']);
                    $map = array_merge($map, $mustNeed);
                    $businessData['businessLicenseData'] = $dataLicense;
                }
                $businessData['hospitalData'] = $data;
                $this->getBusinessLicenseData($id, $businessData, array_column($map, 'id'), $data['customer_type']);
            } else {
//                $data['data_maintenance'] = array_map(function ($v) {
//                    return (int)$v;
//                }, explode(",", $data['data_maintenance']));
                $businessData['hospitalData'] = $data;
//                if ($data['data_maintenance'] != '' && $data['data_maintenance'][0] != 0) {
//                    array_push($map, [
//                        "id" => 6,
//                        "name" => "医疗机构执业许可证",
//                        "component" => "practicing_license",
//                    ],
//                        [
//                            "id" => 5,
//                            "name" => "资质与附件",
//                            "component" => "attachment",
//                        ]);
//                    $businessData['practicingLicenseData'] = CustomerPracticingLicense::where('customer_info_id', $id)->find();
//                    if (!empty($businessData['practicingLicenseData']['equipment_class'])) {
//
//                        foreach (explode(",", $businessData['practicingLicenseData']['equipment_class']) as $equipment_class) {
//                            $equipmentClass[] = (int)$equipment_class;
//                        }
//                        $businessData['practicingLicenseData']['equipment_class'] = $equipmentClass;
//                    }
//                    $businessData['businessAttachmentData'] = $this->getDefaultAtta($this->businessAttachmentModel->where('customer_info_id', $id)->find(), $data['customer_type']);
//                }
            }
            $businessData['id'] = $id;
        }
        return CatchResponse::success([
            'componentData' => $map,
            'customerData' => $businessData,
            'businessScope' => $this->getBusinessScope(),
        ]);
    }

    /**
     * 获取数据
     *
     * @param $dataMaintenance
     * @return array
     * @author 1131191695@qq.com
     */
    private function getComponentData($dataMaintenance, $customer_type): array
    {
        $mustNeed = [];
        foreach ($dataMaintenance as $value) {
            if ($value == 1) {
                array_push($mustNeed, [
                    "id" => 2,
                    "name" => "经营许可证",
                    "component" => "operating_license",
                ]);
            } else if ($value == 2) {
                array_push($mustNeed, [
                    "id" => 3,
                    "name" => "经营备案凭证",
                    "component" => "registration_license",
                ]);
            } else if ($value == 3) {
                array_push($mustNeed, [
                    "id" => 4,
                    "name" => "医疗机构执业许可证",
                    "component" => "practicing_license",
                ]);
            }
        }
        if ($customer_type == 1) {
            array_push($mustNeed,
                [
                    "id" => 5,
                    "name" => "补充信息",
                    "component" => "supplementary",
                ]
            );
        }
        array_push($mustNeed,
            [
                "id" => 6,
                "name" => "资质与附件",
                "component" => "attachment",
            ]
        );
        return $mustNeed;
    }

    /**
     * 获取数据
     *
     * @param int $customer_info_id
     * @param array $businessData
     * @param array $ids
     * @author 1131191695@qq.com
     */
    private function getBusinessLicenseData(int $customer_info_id, array &$businessData, array $ids, $customer_type): void
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
                'name' => 'practicingLicenseData',
                'model' => new CustomerPracticingLicense(),
            ],
            5 => [
                'name' => 'suppleInfoData',
                'model' => $this->customerSuppleInfo
            ],
            6 => [
                'name' => 'businessAttachmentData',
                'model' => $this->businessAttachmentModel,
            ],
        ];
        foreach ($ids as $id) {
            if (!isset($infoHandle[$id])) {
                continue;
            }
            $data = $infoHandle[$id]['model']->where("customer_info_id", $customer_info_id)->find();
            if (!empty($data)) {
                $data = $data->toArray();
                if (isset($infoHandle[$id]['handle'])) {
                    $data = $infoHandle[$id]['handle']($data);
                }
            }
            $businessData[$infoHandle[$id]['name']] = $data ?: "";
        }
        $businessData['businessAttachmentData'] = $this->getDefaultAtta($businessData['businessAttachmentData'], $customer_type);
    }

    /**
     * 获取默认资质附件
     *
     * @param $businessAttachmentData
     * @param $customer_type
     * @return array
     */
    public function getDefaultAtta($businessAttachmentData, $customer_type)
    {
        $map = [];
        foreach ($this->attr as $k => $value) {
            if ($customer_type != 1 && $k != 'practicing_license_institution_url') {
                continue;
            }
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
     * @return array
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
        $model = $this->businessAttachmentModel->where('customer_info_id', $data['customer_info_id'])->find();
        if ($model) {
            $this->businessAttachmentModel->updateBy($model['id'], [
                $data['nKey'] => $data['url']
            ]);
        } else {
            $this->businessAttachmentModel->insert([
                'customer_info_id' => $data['customer_info_id'],
                $data['nKey'] => $data['url']
            ]);
        }
        return CatchResponse::ok('文件上传成功');
    }
}