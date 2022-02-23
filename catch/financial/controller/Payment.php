<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/13
 * Time: 21:20
 */

namespace catchAdmin\financial\controller;


use catchAdmin\financial\model\Receivable as ReceivableModel;
use catcher\base\CatchController;
use catchAdmin\financial\model\Payment as PaymentModel;
use catcher\CatchResponse;
use think\Request;

/**
 * Class Payment
 * @package catchAdmin\financial\controller
 * @note 付款单
 */
class Payment extends CatchController
{
    protected $paymentModel;

    public function __construct(
        PaymentModel $paymentModel
    )
    {
        $this->paymentModel = $paymentModel;
    }

    /**
     * 列表
     *
     * @return \think\response\Json
     * @author xiejiaqing
     */
    public function index()
    {
        return CatchResponse::paginate($this->paymentModel->getList());
    }

    /**
     * 添加
     *
     * @param Request $request
     * @return \think\response\Json
     * @author xiejiaqing
     */
    public function save(Request $request)
    {
        $params = $request->param();
        $b = $this->paymentModel->save($params);
        if ($b) {
            return CatchResponse::success();
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
     * @author xiejiaqing
     */
    public function update(Request $request)
    {
        $params = $request->param();
        if ($params['id']) {
            return CatchResponse::fail("未选择回款单");
        }
        $data = $this->paymentModel->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("回款单已审核,无法修改");
        }
        if ($this->paymentModel->updateBy($params['id'], $params)) {
            return CatchResponse::success();
        }
        return CatchResponse::fail();
    }

    /**
     * 审核
     *
     * @param Request $request
     * @return \think\response\Json|void
     * @author xiejiaqing
     */
    public function audio(Request $request)
    {
        $params = $request->param();
        $data = $this->paymentModel->findBy($params['id']);
        if (!$data) {
            return CatchResponse::fail("数据不存在");
        }
        if ($data['audit_status'] == 1) {
            return CatchResponse::fail("回款单已审核,无法修改");
        }
        // 修改采购订单状态
        // 修改当前回款单状态
    }
}