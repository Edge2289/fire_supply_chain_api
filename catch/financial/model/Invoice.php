<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 20:02
 */

namespace catchAdmin\financial\model;


use catcher\base\CatchModel;
use think\model\relation\HasMany;

/**
 * Class Invoice
 * @package catchAdmin\financial\model
 */
class Invoice extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'invoice_sheet';

    protected $pk = 'id';
    protected $fieldToTime = ['invoice_time'];

    /**
     * @return HasMany
     * @author 1131191695@qq.com
     */
    public function manyInvoiceSheetSource(): HasMany
    {
        return $this->hasMany(InvoiceSheet::class, "invoice_sheet_id", "id");
    }

    /**
     * @return array|mixed
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function getList()
    {
        return $this->with([
            "manyInvoiceSheet",
            "manyInvoiceSheet.hasOutboundOrder",
            "manyInvoiceSheet.hasOutboundOrder.hasSupplierLicense"
        ])->catchSearch()->order("id desc")
            ->paginate();
    }
}