<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/13
 * Time: 20:46
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class FactoryProduction
 * @package catchAdmin\basisinfo\model
 */
class FactoryProduction extends CatchModel
{
    protected $name = "factory_production_license";

    protected $pk = 'id';

    protected $field = [
        'id',
        'factory_id',
        'production_license_url',
        'license_code',
        'business_start_date',
        'business_end_date',
        'company_name',
        'legal_person',
        'head_name',
        'production_address',
        'residence',
        'production_scope',
        'license_department',
        'license_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getBusinessStartDateAttr($value)
    {
        return date("Y-m-d", $value);
    }

    public function getBusinessEndDateAttr($value)
    {
        return date("Y-m-d", $value);
    }

    public function getLicenseDateAttr($value)
    {
        return date("Y-m-d", $value);
    }
}