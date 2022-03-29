<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/29
 * Time: 23:10
 */

namespace catchAdmin\inventory\model;


use catcher\base\CatchModel;

/**
 * Class ConsignmentOutboundDetails
 * @package catchAdmin\inventory\model
 */
class ConsignmentOutboundDetails extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'consignment_outbound_details';

    protected $pk = 'id';
}