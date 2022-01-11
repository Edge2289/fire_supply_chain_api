<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/8
 * Time: 17:48
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class RegistrationLicense
 * @package catchAdmin\basisinfo\model
 */
class RegistrationLicense extends CatchModel
{
    protected $name = 'registration_license';

    protected $pk = 'id';

    protected $field = [
        'id',
        'business_license_id',
        'registration_license_code',
        'registration_date',
        'operation_mode',
        'company_name',
        'legal_person',
        'incharge_person',
        'premise',
        'residence',
        'warehouse_address',
        'equipment_class'
    ];

    public function getRegistrationDateAttr($value)
    {
        return date("Y-m-d", $value);
    }
}