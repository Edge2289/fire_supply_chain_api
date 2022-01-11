<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/8
 * Time: 17:49
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class SuppleInfo
 * @package catchAdmin\basisinfo\model
 */
class SuppleInfo extends CatchModel
{
    protected $name = 'supple_info';

    protected $pk = 'id';

    protected $field = [
        'id',
        'business_license_id',
        'name',
        'certid',
        'phone',
        'email',
        'license_start_date',
        'license_end_date',
        'license_date_long',
        'license_area',
        'product_line',
        'other',
        'invoice_head',
        'invoice_no',
        'invoice_bank',
        'invoice_bank_no',
        'company_address',
        'company_phone'
    ];

    public function getLicenseStartDateAttr($value)
    {
        return date("Y-m-d", $value);
    }
    public function getLicenseEndDateAttr($value)
    {
        return date("Y-m-d", $value);
    }
}