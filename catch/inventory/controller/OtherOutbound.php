<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/27
 * Time: 21:38
 */

namespace catchAdmin\inventory\controller;


use catchAdmin\inventory\model\OtherOutboundDetails;
use catcher\base\CatchController;
use catchAdmin\inventory\model\Warehouse;
use catchAdmin\inventory\model\OtherOutbound as OtherOutboundModel;
use catcher\CatchResponse;

/**
 * 其他出库
 *
 * Class OtherOutbound
 * @package catchAdmin\inventory\controller
 */
class OtherOutbound extends CatchController
{
    protected $warehouse;
    protected $otherOutboundModel;
    protected $otherOutboundDetails;

    public function __construct(
        Warehouse            $warehouse,
        OtherOutboundModel   $otherOutboundModel,
        OtherOutboundDetails $otherOutboundDetails
    )
    {
        $this->warehouse = $warehouse;
        $this->otherOutboundModel = $otherOutboundModel;
        $this->otherOutboundDetails = $otherOutboundDetails;
    }

    /**
     * 列表
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function index()
    {
        return CatchResponse::paginate($this->otherOutboundModel->getList());
    }

    public function save()
    {

    }

    public function audit()
    {

    }

    public function invalid()
    {
        
    }
}