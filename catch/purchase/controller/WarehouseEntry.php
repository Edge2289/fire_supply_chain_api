<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/17
 * Time: 22:17
 */

namespace catchAdmin\purchase\controller;


use catchAdmin\purchase\model\PurchaseOrder as PurchaseOrderModel;
use catcher\base\CatchController;
use app\Request;
use catcher\CatchResponse;

/**
 * Class WarehouseEntry
 * @package catchAdmin\purchase\controller
 */
class WarehouseEntry extends CatchController
{
    protected $purchaseOrderModel;

    public function __construct(
        PurchaseOrderModel $purchaseOrderModel,
    )
    {
        $this->purchaseOrderModel = $purchaseOrderModel;
    }

    public function index()
    {

    }

    // 录入
    public function fnEntering(Request $request)
    {
        // 采购订单必填
        // 必要检查
        $params = $request->param();

        $purchaseOrderMap = $this->purchaseOrderModel->findBy($params['purchaser_order_id']);
        if (empty($purchaseOrderMap)) {
            return CatchResponse::fail("采购订单不存在");
        }
    }
}