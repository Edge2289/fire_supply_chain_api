<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/13
 * Time: 21:20
 */

namespace catchAdmin\financial\controller;


use catcher\base\CatchController;
use catchAdmin\financial\model\Payment as PaymentModel;

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
}