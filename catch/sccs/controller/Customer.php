<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/5/7
 * Time: 17:51
 */

namespace catchAdmin\sccs\controller;


use app\Request;
use catchAdmin\permissions\model\Users;
use catcher\base\CatchController;

/**
 * Class Customer
 * @package catchAdmin\sccs\controller
 */
class Customer extends CatchController
{
    /**
     * @param Request $request
     * @return mixed
     * @author 1131191695@qq.com
     */
    public function save(Request $request)
    {
        $data = app(\catchAdmin\basisinfo\controller\Customer::class)->save($request);
        if (\Request()->user()->customer_id == 0 && isset($data->getData()['data']['id'])) {
            $customerId = $data->getData()['data']['id'];
            Users::where('id', Request()->user()->id)->update(['customer_id' => $customerId]);
        }
        return $data;
    }

    /**
     * 客户信息
     *
     * @param Request $request
     * @return mixed
     * @author 1131191695@qq.com
     */
    public function changeCustomerSetting(Request $request)
    {
        $customerId = \Request()->user()->customer_id;
        return app(\catchAdmin\basisinfo\controller\Customer::class)->changeCustomerSetting($request, $customerId);
    }
}