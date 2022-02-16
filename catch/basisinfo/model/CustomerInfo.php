<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
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

    /**
     * @return \think\model\relation\HasOne
     * @author xiejiaqing
     */
    public function hasCustomerLicense()
    {
        return $this->hasOne(CustomerLicense::class, "customer_info_id", "id");
    }

    /**
     * @return mixed|\think\Paginator
     * @throws \think\db\exception\DbException
     * @author xiejiaqing
     */
    public function getList()
    {
        return $this->with(["hasCustomerLicense"])->catchSearch()->order("id desc")
            ->paginate();
    }

    public function getEffectiveStartDateAttr($value)
    {
        return $this->toDate($value);
    }

    public function getEffectiveEndDateAttr($value)
    {
        return $this->toDate($value);
    }

    public function getCertificationDateAttr($value)
    {
        return $this->toDate($value);
    }

}