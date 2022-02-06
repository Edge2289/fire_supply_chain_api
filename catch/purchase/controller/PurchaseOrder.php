<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/27
 * Time: 16:39
 */

namespace catchAdmin\purchase\controller;


use app\Request;
use catcher\base\CatchController;
use catchAdmin\purchase\model\PurchaseOrder as PurchaseOrderModel;
use catcher\CatchResponse;

/**
 * 采购订单
 * Class PurchaseOrder
 * @package catchAdmin\purchase\controller
 */
class PurchaseOrder extends CatchController
{
    protected $purchaseOrderModel;

    public function __construct(
        PurchaseOrderModel $purchaseOrderModel
    )
    {
        // 构造函数
        $this->purchaseOrderModel = $purchaseOrderModel;
    }

    // 列表
    public function index()
    {
        return CatchResponse::paginate($this->purchaseOrderModel->getList());
    }

    // 保存
    public function save(Request $request)
    {
        // 保存基础信息
        $params = $request->param();
        // 保存商品
        $goodsDetails = $params['goods_details'];
    }

    // 更新
    public function update()
    {

    }

    // 审核
    public function audit()
    {

    }

    // 取消
    public function cancel()
    {

    }
}