<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/17
 * Time: 22:17
 */

namespace catchAdmin\purchase\controller;


use catchAdmin\purchase\model\ProcurementWarehousingDetails;
use catchAdmin\purchase\model\PurchaseOrder as PurchaseOrderModel;
use catcher\base\CatchController;
use app\Request;
use catcher\CatchResponse;
use catchAdmin\purchase\model\ProcurementWarehousing as ProcurementWarehousingModel;

/**
 * 采购入库
 * Class ProcurementWarehousing
 * @package catchAdmin\purchase\controller
 */
class ProcurementWarehousing extends CatchController
{
    protected $purchaseOrderModel;
    protected $procurementWarehousing;
    protected $procurementWarehousingDetails;

    public function __construct(
        PurchaseOrderModel            $purchaseOrderModel,
        ProcurementWarehousingModel   $procurementWarehousing,
        ProcurementWarehousingDetails $procurementWarehousingDetails
    )
    {
        $this->purchaseOrderModel = $purchaseOrderModel;
        $this->procurementWarehousing = $procurementWarehousing;
        $this->procurementWarehousingDetails = $procurementWarehousingDetails;
    }

    public function index()
    {
        return CatchResponse::paginate($this->procurementWarehousing->getList());
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

        /**
         * 入库成功的话，添加库存
         */
    }

    /**
     * 更新
     *
     * @param Request $request
     * @author xiejiaqing
     */
    public function fnUpdate(Request $request)
    {
        // 保存
    }

}