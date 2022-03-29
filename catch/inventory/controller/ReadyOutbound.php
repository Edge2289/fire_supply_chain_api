<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/27
 * Time: 21:38
 */

namespace catchAdmin\inventory\controller;


use catchAdmin\inventory\model\ReadyOutboundDetails;
use catcher\base\CatchController;
use catchAdmin\inventory\model\ReadyOutbound as ReadyOutboundModel;
use catcher\CatchResponse;

/**
 * 备货出库
 *
 * Class ReadyOutbound
 * @package catchAdmin\inventory\controller
 */
class ReadyOutbound extends CatchController
{
    protected $readyOutboundModel;
    protected $readyOutboundDetails;

    public function __construct(
        ReadyOutboundModel   $readyOutboundModel,
        ReadyOutboundDetails $readyOutboundDetails
    )
    {
        $this->readyOutboundModel = $readyOutboundModel;
        $this->readyOutboundDetails = $readyOutboundDetails;
    }

    public function index()
    {
        return CatchResponse::paginate($this->readyOutboundModel->getList());
    }
}