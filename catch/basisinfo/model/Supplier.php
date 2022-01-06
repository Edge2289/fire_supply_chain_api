<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/6
 * Time: 09:44
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class Supplier
 * @package catchAdmin\basisinfo\model
 */
class Supplier extends CatchModel
{

    // 字段
    protected $field = [
        'id', //
        'job_name', // 岗位名称
        'coding', // 编码
        'creator_id', // 创建人ID
        'status', // 1 正常 2 停用
        'sort', // 排序字段
        'description', // 描述
        'created_at', // 创建时间
        'updated_at', // 更新时间
        'deleted_at', // 删除状态，null 未删除 timestamp 已删除
    ];
}