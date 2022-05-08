<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/5/7
 * Time: 17:52
 */

namespace catchAdmin\sccs\controller;


use app\Request;
use catchAdmin\inventory\model\ConsignmentOutbound;
use catchAdmin\inventory\model\ConsignmentOutboundDetails;
use catchAdmin\inventory\model\ReadyOutbound;
use catchAdmin\inventory\model\ReadyOutboundDetails;
use catchAdmin\inventory\model\TurnSalesRecord;
use catchAdmin\salesManage\model\SalesOrderDetailsModel;
use catchAdmin\salesManage\model\SalesOrderModel;
use catcher\base\CatchController;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use catcher\Utils;
use fire\data\ChangeStatus;
use think\db\exception\DbException;
use think\response\Json;

/**
 * Class SalesOrder
 * @package catchAdmin\sccs\controller
 */
class SalesOrder extends CatchController
{

    protected $salesOrderModel;
    protected $salesOrderDetailsModel;

    public function __construct(
        SalesOrderModel        $salesOrderModel,
        SalesOrderDetailsModel $salesOrderDetailsModel
    )
    {
        $this->salesOrderModel = $salesOrderModel;
        $this->salesOrderDetailsModel = $salesOrderDetailsModel;
    }

    /**
     * @return Json
     * @throws DbException
     * @author 1131191695@qq.com
     */
    public function index()
    {
        // customer_info_id
        $customerId = Request()->user()->customer_id;
        $data = $this->salesOrderModel->getList(['customer_info_id' => $customerId]);
        foreach ($data as &$datum) {
            $datum['settlement_status_i'] = $datum['settlement_status'] == 0 ? "未结" : "已结";
            $datum['settlement_type_i'] = $datum['settlement_type'] == 0 ? "现结" : "月结";
        }
        ChangeStatus::getInstance()->audit()->status()->handle($data);
        return CatchResponse::paginate($data);
    }

    public function save(Request $request)
    {
        $params = $request->param();
        // 保存基础信息
        $goodsDetails = $params['goods_details'];
        unset($params['goods_details']);
        $this->salesOrderModel->startTrans();
        try {
            if (empty($params['salesman_id'])) {
                $params['salesman_id'] = \request()->user()->id;
            }
            $params['sales_time'] = strtotime($params['sales_time']);
            $params['customer_info_id'] = Request()->user()->customer_id;
            $params['customer_code'] = 0;
            if (isset($params["id"]) && !empty($params["id"])) {
                // 存在id 更新操作
                $data = $this->salesOrderModel->findBy($params['id']);
                if ($data['audit_status'] == 1) {
                    return CatchResponse::fail("单据已审核,无法修改");
                }
                if ($data['status'] != 0) {
                    return CatchResponse::fail("单据不为未完成,无法修改");
                }
                $this->salesOrderModel->updateBy($params['id'], $params);
                $id = $params['id'];
                // 删除
                $this->salesOrderDetailsModel->destroy(["sales_order_id" => $params['id']]);
            } else {
                $params['order_code'] = getCode("SO");
                $params['company_id'] = request()->user()->department_id;
                unset($params['id']);
                $id = $this->salesOrderModel->insertGetId($params);
                if (!$id) {
                    throw new BusinessException("销售订单添加失败");
                }
            }
            // 重新添加商品数据
            $skuIds = [];
            $map = [];
            foreach ($goodsDetails as $goodsDetail) {
                if (in_array($goodsDetail['id'], $skuIds)) {
                    throw new BusinessException("商品数据重复");
                }
                if (empty($goodsDetail['entity']) || empty($goodsDetail['unit_price'])) {
                    throw new BusinessException("商品数据填写不完整");
                }
                $skuIds[] = $goodsDetail['id'];
                $map[] = [
                    'sales_order_id' => $id,
                    'product_id' => $goodsDetail['product_id'] ?? 0,
                    'product_sku_id' => $goodsDetail['id'],
                    'product_code' => $goodsDetail['product_code'],
                    'item_number' => $goodsDetail['item_number'],
                    'entity' => $goodsDetail['entity'],
                    'sku_code' => $goodsDetail['sku_code'],
                    'unit_price' => $goodsDetail['unit_price'],
                    'tax_rate' => Utils::config('product.tax'),
                    'quantity' => $goodsDetail['quantity'],
                    'delivery_number' => 0,
                    'note' => $goodsDetail['note'] ?? "",
                ];
            }
            if (empty($map)) {
                throw new BusinessException("商品数据为空");
            }
            $gId = $this->salesOrderDetailsModel->insertAll($map);
            if (empty($gId)) {
                throw new \Exception("销售订单商品添加失败");
            }
            // 提交事务
            $this->salesOrderModel->commit();
        } catch (\Exception $exception) {
            // 回滚事务
            $this->salesOrderModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
        return CatchResponse::success(['id' => $id]);
    }

    /**
     * 作废
     *
     * @param Request $request
     * @return Json
     * @author 1131191695@qq.com
     */
    public function invalid(Request $request)
    {
        $data = $request->param();
        $this->salesOrderModel->startTrans();
        try {
            $data = $this->salesOrderModel->getFindByKey($data['id']);
            if ($data['audit_status'] != 0) {
                throw new BusinessException("订单已审核，无法作废");
            }
            if ($data['status'] != 0) {
                throw new BusinessException("订单状态不为未完成，无法作废");
            }
            $this->salesOrderModel->updateBy($data['id'], [
                'status' => 2
            ]);
            $this->salesOrderModel->commit();
        } catch (\Exception $exception) {
            $this->salesOrderModel->rollback();
            throw new BusinessException($exception->getMessage());
        }
        return CatchResponse::success();
    }
}