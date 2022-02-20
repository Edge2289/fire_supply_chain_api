<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/13
 * Time: 21:21
 */

namespace catchAdmin\financial\controller;


use catcher\base\CatchController;
use catchAdmin\financial\model\Invoice as InvoiceModel;

/**
 * Class Invoice
 * @package catchAdmin\financial\controller
 * @note 开票
 */
class Invoice extends CatchController
{
    protected $invoiceModel;

    public function __construct(
        InvoiceModel $invoiceModel
    )
    {
        $this->invoiceModel = $invoiceModel;
    }
}