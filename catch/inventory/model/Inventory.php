<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/25
 * Time: 17:17
 */

namespace catchAdmin\inventory\model;


use catcher\base\CatchModel;

/**
 * Class Inventory
 * @package catchAdmin\inventory\model
 */
class Inventory extends CatchModel
{
    protected $connection = 'business';

    protected $name = 'inventory';

    protected $pk = 'id';

    public function getList()
    {
        return $this->catchSearch()
            ->paginate();
    }

    public function getWarehouse()
    {
        return $this->where("company_id", request()->user()->company_id)->select()->toArray();
    }
}