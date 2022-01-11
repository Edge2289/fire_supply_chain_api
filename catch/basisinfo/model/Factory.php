<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 15:06
 */

namespace catchAdmin\basisinfo\model;


use catcher\base\CatchModel;

/**
 * Class Factory
 * @package catchAdmin\basisinfo\model
 */
class Factory extends CatchModel
{
    protected $name = 'factory';

    protected $pk = 'id';

    protected $field = [
        'id',
        'scope2017',
        'scope2002'
    ];

    /**
     * 列表
     *
     * @time 2020年01月09日
     * @param $params
     * @throws \think\db\exception\DbException
     * @return \think\Paginator
     */
    public function getList()
    {
        return $this->catchSearch()
            ->paginate();
    }
}