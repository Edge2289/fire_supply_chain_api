<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/13
 * Time: 20:46
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class FactoryRecord
 * @package catchAdmin\basisinfo\model
 */
class FactoryRecord extends CatchModel
{
    protected $name = "factory_record";

    protected $pk = 'id';

    protected $field = [
        'id',
        'factory_id',
        'record_code',
        'company_name',
        'legal_person',
        'head_name',
        'production_address',
        'production_scope',
        'record_department',
        'record_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getRecordDateAttr($value)
    {
        return date("Y-m-d", $value);
    }

}