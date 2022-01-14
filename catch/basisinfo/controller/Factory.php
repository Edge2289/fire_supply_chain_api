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
use catchAdmin\basisinfo\model\FactoryProduction;
use catchAdmin\basisinfo\model\FactoryRecord;
use catchAdmin\basisinfo\request\FactoryRequest;
use catcher\base\CatchController;
use catcher\base\CatchRequest;
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
     * @author xiejiaqing
     */
    public function index()
    {
        return CatchResponse::paginate($this->factory->getList());
    }

    /**
     * 获取设置
     *
     * @author xiejiaqing
     * @param Request $request
     * @return \think\response\Json
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
     * @author xiejiaqing
     * @param $id
     * @param $factoryData
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function factoryAuxiliary($id, &$factoryData)
    {
        $factoryData['productionLicense'] = $this->factoryProduction->where("factory_id", $id)->find();
        $factoryData['record'] = $this->factoryRecord->where("factory_id", $id)->find();
    }

    /**
     * 保存
     *
     * @author xiejiaqing
     * @param Request $request
     * @return \think\response\Json
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
     * @author xiejiaqing
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
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
     * @author xiejiaqing
     * @param array $data
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
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
            return $this->factory->storeBy($map);
        }
    }

    /**
     * 生产许可证
     *
     * @author xiejiaqing
     * @param array $data
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
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
     * @author xiejiaqing
     * @param array $data
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException\
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

    public function audit()
    {

    }

    public function delete()
    {

    }
}