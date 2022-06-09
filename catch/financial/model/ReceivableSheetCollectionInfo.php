<?php
/**
 * Created by PhpStorm.
 * author: meridian(1131191695@qq.com)
 * Note: Tired as a dog
 * Date: 2022/6/5
 * Time: 14:04
 */

namespace catchAdmin\financial\model;


use catcher\base\CatchModel;

/**
 * Class PaymentSheetCollectionInfo
 * @package catchAdmin\financial\model
 */
class ReceivableSheetCollectionInfo extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'receivable_sheet_collection_info';

    protected $pk = 'id';
}