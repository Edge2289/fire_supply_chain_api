<?php
/**
 * Created by PhpStorm.
 * author: meridian(1131191695@qq.com)
 * Note: Tired as a dog
 * Date: 2022/9/21
 * Time: 11:23
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class FactoryAttachmentUrl
 * @package catchAdmin\basisinfo\model
 */
class FactoryAttachmentUrl extends CatchModel
{
    protected $name = "factory_attachment_url";

    protected $pk = 'id';

    protected $field = [
        'id',
        'factory_id',
        'business_license_url',
        'contract_url',
        'record_license_url',
        'production_license_url',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}