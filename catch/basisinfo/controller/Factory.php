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
            $factoryData['factory'] = $data;
            if ($data['factory_type'] != 2) {
                // 如果是国外公司，直接返回
                $type = explode(",", $data);
                foreach ($type as $value) {
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

    private function factoryAuxiliary($id, &$factoryData)
    {
        $factoryData['productionLicense'] = $this->factoryProduction->find(['factory_id' => $id]);
        $factoryData['record'] = $this->factoryProduction->find(['factory_id' => $id]);
    }

    public function save(CatchRequest $catchRequest)
    {
        dd($catchRequest);
    }

    public function update()
    {

    }

    public function audit()
    {

    }

    public function delete()
    {

    }
}