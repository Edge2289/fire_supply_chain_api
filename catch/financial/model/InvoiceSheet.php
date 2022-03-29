<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/28
 * Time: 17:42
 */

namespace catchAdmin\financial\model;


use catchAdmin\purchase\model\PurchaseOrder;
use catchAdmin\salesManage\model\OutboundOrder;
use catcher\base\CatchModel;
use think\model\relation\HasOne;

/**
 * Class InvoiceSheet
 * @package catchAdmin\financial\model
 */
class InvoiceSheet extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'invoice_many_sales_order';

    protected $pk = 'id';

    /**
     * @return HasOne
     * @author 1131191695@qq.com
     */
    public function hasOutboundOrder(): HasOne
    {
        return $this->hasOne(OutboundOrder::class, "id", "outbound_order_id");
    }
}