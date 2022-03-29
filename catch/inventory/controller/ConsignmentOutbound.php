<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/27
 * Time: 21:38
 */

namespace catchAdmin\inventory\controller;


use catchAdmin\inventory\model\ConsignmentOutboundDetails;
use catcher\base\CatchController;
use catchAdmin\inventory\model\ConsignmentOutbound as ConsignmentOutboundModel;
use catcher\CatchResponse;

/**
 * 寄售出库
 * Class ConsignmentOutbound
 * @package catchAdmin\inventory\controller
 */
class ConsignmentOutbound extends CatchController
{
    protected $consignmentOutboundModel;
    protected $consignmentOutboundDetails;

    public function __construct(
        ConsignmentOutboundModel   $consignmentOutboundModel,
        ConsignmentOutboundDetails $consignmentOutboundDetails
    )
    {
        $this->consignmentOutboundModel = $consignmentOutboundModel;
        $this->consignmentOutboundDetails = $consignmentOutboundDetails;
    }

    public function index()
    {
        return CatchResponse::paginate($this->consignmentOutboundModel->getList());
    }

    public function save()
    {

    }

    public function audit()
    {

    }

    public function turnSales()
    {

    }

}