<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/14
 * Time: 20:47
 */

namespace catchAdmin\salesManage\controller;


use catchAdmin\inventory\model\Inventory;
use catchAdmin\inventory\model\InventoryBatch;
use catchAdmin\inventory\model\Warehouse;
use catchAdmin\salesManage\model\OutboundOrderDetails;
use catcher\base\CatchController;
use catchAdmin\salesManage\model\OutboundOrder as OutboundOrderModel;
use catcher\CatchResponse;
use think\Request;

/**
 * Class OutboundOrder
 * @package catchAdmin\salesManage\controller
 */
class OutboundOrder extends CatchController
{
    // 出库单
    private $outboundOrderModel;
    private $outboundOrderDetails;

    // 仓库
    private $warehouse;
    // 库存
    private $inventory;
    private $inventoryBatch;

    public function __construct(
        OutboundOrderModel   $outboundOrder,
        OutboundOrderDetails $outboundOrderDetails,
        Warehouse            $warehouse,
        Inventory            $inventory,
        InventoryBatch       $inventoryBatch
    )
    {
        $this->outboundOrderModel = $outboundOrder;
        $this->outboundOrderDetails = $outboundOrderDetails;
        $this->warehouse = $warehouse;
        $this->inventory = $inventory;
        $this->inventoryBatch = $inventoryBatch;
    }

    /**
     * 获取列表
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function index()
    {
        $status = [
            "未完成", "已完成", "作废"
        ];
        $data = $this->outboundOrderModel->getList();
        foreach ($data as &$datum) {
            $datum['status_i'] = $status[$datum['status']];
            $datum['settlement_type_i'] = $datum['settlement_type'] == 0 ? "现结" : "月结";
        }
        return CatchResponse::paginate($data);
    }

    // 保存
    public function save(Request $request)
    {
        // 保存基础信息
        $params = $request->param();
    }

    // 审核
    public function audit()
    {

    }

    // 作废返回销售订单的出货数
    public function invalid()
    {

    }
}