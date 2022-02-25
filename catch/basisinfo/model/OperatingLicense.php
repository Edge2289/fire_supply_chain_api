<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/8
 * Time: 17:47
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class OperatingLicense
 * @package catchAdmin\basisinfo\model
 */
class OperatingLicense extends CatchModel
{
    protected $name = 'operating_license';

    protected $pk = 'id';

    protected $field = [
        'id',
        'business_license_id',
        'operating_license_code',
        'operation_mode',
        'company_name',
        'legal_person',
        'incharge_person',
        'business_start_date',
        'business_end_date',
        'business_date_long',
        'premise',
        'residence',
        'warehouse_address',
        'equipment_class'
    ];

    public function getBusinessStartDateAttr($value)
    {
        return date("Y-m-d", $value);
    }

    public function getBusinessEndDateAttr($value)
    {
        return date("Y-m-d", $value);
    }
}