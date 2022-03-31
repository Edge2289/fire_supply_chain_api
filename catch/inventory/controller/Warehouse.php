<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 21:32
 */

namespace catchAdmin\inventory\controller;


use catcher\base\CatchController;
use catcher\base\CatchRequest;
use catcher\CatchResponse;
use \catchAdmin\inventory\model\Warehouse as WarehouseModel;
use catcher\exceptions\BusinessException;

/**
 * Class warehouse
 * @package catchAdmin\inventory\controller
 */
class Warehouse extends CatchController
{

    public $warehouse;

    public function __construct(WarehouseModel $warehouse)
    {
        $this->warehouse = $warehouse;
    }

    public function index()
    {
        $type = [
            1 => '待检库',
            2 => '合格库',
            3 => '不合格库',
            4 => '非医疗器械库',
        ];

        $data = $this->warehouse->getList();
        foreach ($data as &$datum) {
            $datum->warehouse_type_name = $type[$datum->warehouse_type];
        }
        return CatchResponse::paginate($data);
    }

    /**
     * 保存
     *
     * @time 2020年01月09日
     * @param CatchRequest $request
     * @return \think\response\Json
     */
    public function save(CatchRequest $request): \think\response\Json
    {
        return CatchResponse::success($this->warehouse->storeBy(array_merge($request->post(), [
            'company_id' => request()->user()->department_id
        ])));
    }

    /**
     * 更新
     *
     * @time 2020年01月09日
     * @param $id
     * @param CatchRequest $request
     * @return \think\response\Json
     */
    public function update($id, CatchRequest $request): \think\response\Json
    {
        $find = $this->warehouse->find([
            'id' => $id,
            'company_id' => request()->user()->department_id
        ]);
        if (empty($find)) {
            throw new BusinessException("不存在的数据");
        }
        return CatchResponse::success($this->warehouse->updateBy($id, array_merge($request->post(), [
            'company_id' => request()->user()->department_id
        ])));
    }

    /**
     * 删除
     *
     * @time 2020年01月09日
     * @param $id
     * @return \think\response\Json
     */
    public function delete($id): \think\response\Json
    {
        $find = $this->warehouse->find([
            'id' => $id,
            'company_id' => request()->user()->department_id
        ]);
        if (empty($find)) {
            throw new BusinessException("不存在的数据");
        }
        return CatchResponse::success($this->warehouse->deleteBy($id));
    }

    public function tableGetWarehouse()
    {
        return $this->warehouse->tableGetWarehouse();
    }
}