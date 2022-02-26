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
use catchAdmin\basisinfo\model\FactoryProduction;
use catchAdmin\basisinfo\model\FactoryRecord;
use catchAdmin\basisinfo\request\FactoryRequest;
use catcher\base\CatchController;
use catcher\CatchResponse;
use \catchAdmin\basisinfo\model\Factory as FactoryModel;
use catcher\exceptions\BusinessException;

/**
 * 厂家管理
 *
 * Class Factory
 * @package catchAdmin\basisinfo\controller
 */
class Factory extends CatchController
{
    public $factory;
    public $factoryRecord;
    public $factoryProduction;

    public function __construct(
        FactoryModel      $factory,
        FactoryProduction $factoryProduction,
        FactoryRecord     $factoryRecord
    )
    {
        $this->factory = $factory;
        $this->factoryRecord = $factoryRecord;
        $this->factoryProduction = $factoryProduction;
    }

    /**
     * 厂家列表
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function index()
    {
        // 审核状态 {0:未审核,1:已审核,2:审核失败}
        $auditStatusI = [
            "未审核", "已审核", "审核失败"
        ];
        $data = $this->factory->getList();
        foreach ($data as &$datum) {
            $datum['business_end_date_z'] = "";
            if ($datum['business_date_long'] == 1) {
                $datum['business_end_date_z'] = "长期";
            } elseif (!empty($datum['business_end_date'])) {
                $datum['business_end_date_z'] = $datum['business_end_date'];
            }
            $datum['factory_type_name'] = $datum['factory_type'] == 1 ? "国内厂家" : "国外厂家";
            $datum['audit_status_i'] = $auditStatusI[$datum['audit_status']];
        }
        return CatchResponse::paginate($data);
    }

    /**
     * 获取设置
     *
     * @param Request $request
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function changeFactorySetting(Request $request)
    {
        $id = $request->param('id') ?? 0;
        $map = [
            [
                "id" => 1,
                "name" => "厂家信息",
                "component" => "business_license",
            ]
        ];
        $factoryData = [];
        if ($id != 0) {
            $data = $this->factory->findBy($id);
            if (empty($data)) {
                throw new BusinessException("找不到数据");
            }

            if (!empty($data['data_maintenance'])) {
                $data['data_maintenance'] = array_filter(explode(",", $data['data_maintenance']));
            }
            $data['factory_type'] = (string)$data['factory_type'];
            $factoryData['factory'] = $data;
            if ($data['factory_type'] != 2) {
                // 如果是国外公司，直接返回
                foreach ($data['data_maintenance'] as $value) {
                    if ($value == 1) {
                        $map[] = [
                            "id" => 2,
                            "name" => "生产许可",
                            "component" => "production_license",
                        ];
                    } else {
                        $map[] = [
                            "id" => 3,
                            "name" => "备案凭证",
                            "component" => "record",
                        ];
                    }
                }
                $this->factoryAuxiliary($id, $factoryData);
            }
        }

        return CatchResponse::success([
            'componentData' => $map,
            'factoryData' => $factoryData,
        ]);
    }

    /**
     * 自定义
     *
     * @param $id
     * @param $factoryData
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    private function factoryAuxiliary($id, &$factoryData)
    {
        $factoryData['productionLicense'] = $this->factoryProduction->where("factory_id", $id)->find();
        $factoryData['record'] = $this->factoryRecord->where("factory_id", $id)->find();
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
        $type = $request->param('form_factory_type') ?? "";
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
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function update(Request $request)
    {
        $type = $request->param('form_factory_type') ?? "";
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
     * 厂家信息
     *
     * @param array $data
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    protected function factoryCall(array $data)
    {
        // 厂家信息
        $map = [];
        if ($data['factory_type'] == 2) {
            if (empty($data['company_name']) || empty($data['company_name_en'])) {
                throw new BusinessException("请填写完整信息");
            }
            $map = [
                'id' => $data['id'] ?? 0,
                'factory_type' => $data['factory_type'],
                'company_name' => $data['company_name'],
                'company_name_en' => $data['company_name_en'],
            ];
        } else {
            $this->validator(FactoryRequest::class, $map);
            $map = $data;
            $map['business_start_date'] = strtotime($map['business_start_date']);
            $map['business_end_date'] = strtotime($map['business_end_date']);
            $map['establish_date'] = strtotime($map['establish_date']);
            $map['registration_date'] = strtotime($map['registration_date']);
            unset($map['form_factory_type']);
        }
        if (isset($map['id']) && !empty($map['id'])) {
            $data = $this->factory->where('id', $map['id'])->find();
            if (!$data) {
                throw new BusinessException("数据不存在");
            }
            return $this->factory->updateBy($map['id'], $map);
        } else {
            $map['factory_code'] = getCode("FC");
            return $this->factory->storeBy($map);
        }
    }

    /**
     * 生产许可证
     *
     * @param array $data
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    protected function productionLicenseCall(array $data)
    {
        if ($data['id']) {
            // 存在
            return $this->factoryProduction->updateBy($data['id'], $data);
        }
        return $this->factoryProduction->storeBy($data);
    }

    /**
     * 备案
     *
     * @param array $data
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException\
     * @author 1131191695@qq.com
     */
    protected function recordCall(array $data)
    {
        $data['record_date'] = strtotime($data['record_date']);
        if ($data['id']) {
            // 存在
            return $this->factoryRecord->updateBy($data['id'], $data);
        }
        unset($data['form_factory_type']);
        return $this->factoryRecord->storeBy($data);
    }

    /**
     * 审核
     *
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
        $factoryData = $this->factory->findBy($data['id']);
        if (empty($factoryData)) {
            throw new BusinessException("不存在产品");
        }
        if ($factoryData['audit'] == 1) {
            return CatchResponse::fail("已审核");
        }
        $b = $this->factory->updateBy($data['id'], [
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
}