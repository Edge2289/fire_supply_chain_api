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
use catchAdmin\financial\model\Receivable as ReceivableModel;
use catcher\CatchResponse;

/**
 * Class Receivable
 * @package catchAdmin\financial\controller
 * @note 回款单
 */
class Receivable extends CatchController
{
    protected $receivableModel;

    public function __construct(
        ReceivableModel $receivableModel
    )
    {
        $this->receivableModel = $receivableModel;
    }

    public function index()
    {
        return CatchResponse::paginate($this->receivableModel->getList());
    }
}