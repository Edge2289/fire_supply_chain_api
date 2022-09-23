<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/13
 * Time: 20:48
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class CustomerInfo
 * @package catchAdmin\basisinfo\model
 */
class CustomerInfo extends CatchModel
{
    protected $name = "customer_info";

    protected $pk = 'id';

    protected $fieldToTime = ['effective_start_date', 'effective_end_date', 'certification_date'];

    /**
     * @return \think\model\relation\HasOne
     * @author 1131191695@qq.com
     */
    public function hasCustomerLicense()
    {
        return $this->hasOne(CustomerLicense::class, "customer_info_id", "id");
    }

    /**
     * @return mixed|\think\Paginator
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function getList()
    {
        return $this->with(["hasCustomerLicense"])->catchSearch()->order("id desc")
            ->paginate();
    }

    /**
     * 组件客户接口
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getFormLier()
    {
        $data = $this->where("status", 1)
            ->where("audit_status", 1)->select();
        $map = [];

        foreach ($data as $datum) {
            $company_name = $datum['company_name'];
            if ($datum['customer_type'] == 1) {
                $company_name = $datum['hasCustomerLicense']["company_name"] ?? '';
            }
            $map[] = [
                'value' => (string)$datum['id'],
                'label' => $company_name,
            ];
        }
        return $map;
    }

}