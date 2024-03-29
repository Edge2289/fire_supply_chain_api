<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 14:54
 */

namespace catchAdmin\purchase\model;


use catchAdmin\basisinfo\model\ProductBasicInfo;
use catcher\base\CatchModel;

/**
 * Class ProcurementWarehousingDetails
 * @package catchAdmin\purchase\model
 */
class ProcurementWarehousingDetails extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'procurement_warehousing_details';

    protected $pk = 'id';

    public function hasProductBasicInfo()
    {
        return $this->hasOne(ProductBasicInfo::class, "id", "product_id");
    }
}