<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
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
use catchAdmin\basisinfo\model\EquipmentClass;
use catcher\base\CatchController;
use catchAdmin\basisinfo\model\CustomerLicense as CustomerModel;
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
    public $equipmentClassModel;
    public $operatingLicenseModel;
    public $registrationLicenseModel;
    public $customerInfoModel;
    public $businessAttachmentModel;

    public function __construct(
        CustomerModel  $customerModel,
        EquipmentClass      $equipmentClassModel,
        CustomerOperating    $customerOperating,
        CustomerRegistration $customerInfoModel,
        CustomerInfo          $customerInfo,
        CustomerAttachment  $attachment
    )
    {
        $this->customerModel = $customerModel;
        $this->equipmentClassModel = $equipmentClassModel;
        $this->operatingLicenseModel = $customerOperating;
        $this->registrationLicenseModel = $customerInfoModel;
        $this->customerInfoModel = $customerInfo;
        $this->businessAttachmentModel = $attachment;
    }

    /**
     * @throws \think\db\exception\DbException
     */
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

    public function open()
    {

    }

    public function disabled()
    {

    }

//    ----------------- help --------------------

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
    public function changeCustomerSetting(Request $request)
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
            $data = $this->customerModel->find(['id' => $id]);
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
            'customerData' => $businessData,
            'businessScope' => $this->getBusinessScope(),
        ]);
    }

    /**
     * 获取数据
     *
     * @author xiejiaqing
     * @param $dataMaintenance
     * @return array
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
     * @author xiejiaqing
     * @param int $business_license_id
     * @param array $businessData
     * @param array $ids
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
                'model' => $this->customerInfoModel
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
            $data = $infoHandle[$id]['model']->where("business_license_id", $business_license_id)->find();
            if (!empty($data)) {
                $data = $data->toArray();
                if (isset($infoHandle[$id]['handle'])) {
                    $data = $infoHandle[$id]['handle']($data);
                }
            }
            $businessData[$infoHandle[$id]['name']] = $data ?: [];
        }
    }

    /**
     * 获取医疗器械分类目录
     *
     * @author xiejiaqing
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getBusinessScope(): array
    {
        return $this->equipmentClassModel->select()->toArray();
    }
}