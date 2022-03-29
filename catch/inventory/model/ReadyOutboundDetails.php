<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/29
 * Time: 23:11
 */

namespace catchAdmin\inventory\model;


use catcher\base\CatchModel;

/**
 * Class ReadyOutboundDetails
 * @package catchAdmin\inventory\model
 */
class ReadyOutboundDetails extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'ready_outbound_details';

    protected $pk = 'id';
}