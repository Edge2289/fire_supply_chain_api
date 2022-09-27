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
        foreach ($data as &$datum) {
            if ($datum['customer_type'] == 1) {
                $datum['company_name'] = $datum['hasCustomerLicense']["company_name"] ?? '';
                $datum['effective_end_date'] = ($datum['hasCustomerLicense']['business_date_long'] ?? 0) == 1 ? "长期" : $datum['business_end_date'];
                $datum['legal_person'] = $datum['hasCustomerLicense']['legal_person'] ?? '';
            }
            $datum['customer_type'] = $datum['customer_type'] == 1 ? "经销商" : "医院";
        }
        ChangeStatus::getInstance()->audit()->handle($data);
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
            }
            $map = $request->param();
            if ($type == 1) {
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
        if ($type != 1) {
            if (!$id) {
                return CatchResponse::fail("缺失客户id");
            }
            $data = $this->customerInfoModel->findBy($id);
            if (empty($data)) {
                return CatchResponse::fail("客户数据不存在");
            }
            // 医院内容
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
            $result = $this->{$type}(array_merge($map, [
                "customer_info_id" => $id
            ]));
        }
        if ($result) {
            return CatchResponse::success([
                'id' => $id
            ]);
        }
        return CatchResponse::fail();
    }

    /**
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
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

    public function open()
    {

    }

    public function disabled()
    {

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
        }
        $params['establish_date'] = strtotime($params['establish_date']);
        unset($params['suppliers_type']);

//        $where = $this->customerSuppleInfo->where("unified_code", $params['unified_code']);
//        if (isset($params['id']) && !empty($params['id'])) {
//            $where = $where->where("id", "<>", $params['id']);
//        }
        $this->validator(CustomerSupplierLicenseRequest::class, $params);
//        $data = $where->find();
//        if (!empty($data)) {
//            throw new BusinessException("统一社会信用码已存在");
//        }
        if (isset($params['id']) && !empty($params['id'])) {
            $data = $this->customerLicenseModel->where('id', $params['id'])->find();
            if (!$data) {
                throw new BusinessException("数据不存在");
            }
            return $this->customerLicenseModel->updateBy($params['id'], $params);
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
        $params['license_end_date'] = strtotime($params['license_end_date']);
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
            if ($data['customer_type'] == 1) {
                $dataLicense = $this->customerLicenseModel->where("customer_info_id", $id)->find();
                if (empty($dataLicense)) {
                    $dataLicense['data_maintenance'] = [];
                    $businessData['businessLicenseData'] = [];
                } else {
                    $dataLicense['data_maintenance'] = explode(",", $dataLicense['data_maintenance']);
                    $mustNeed = $this->getComponentData($dataLicense['data_maintenance']);
                    $map = array_merge($map, $mustNeed);
                    $businessData['businessLicenseData'] = $dataLicense;
                }
                $this->getBusinessLicenseData($id, $businessData, array_column($map, 'id'));
            } else {
                $businessData['hospitalData'] = $data;
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
     * @param int $customer_info_id
     * @param array $businessData
     * @param array $ids
     * @author 1131191695@qq.com
     */
    private function getBusinessLicenseData(int $customer_info_id, array &$businessData, array $ids): void
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
                'model' => $this->customerSuppleInfo
            ],
            5 => [
                'name' => 'businessAttachmentData',
                'model' => $this->businessAttachmentModel,
                'handle' => function ($data) {
                    foreach ($data as $key => $param) {
                        if (strpos($key, "check") === false) {
                            continue;
                        }
                        $data[$key] = [$param];
                    }
                    return $data;
                }
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
}