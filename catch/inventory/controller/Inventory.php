<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/4
 * Time: 16:06
 */

namespace catchAdmin\inventory\controller;


use catchAdmin\inventory\model\InventoryBatch;
use catcher\base\CatchController;
use catchAdmin\inventory\model\Inventory as InventoryModel;
use catcher\CatchResponse;

/**
 * Class Inventory
 * @package catchAdmin\inventory\controller
 */
class Inventory extends CatchController
{
    protected $inventoryModel;
    protected $inventoryBatchModel;

    public function __construct(
        InventoryModel $inventoryModel,
        InventoryBatch $inventoryBatchModel
    )
    {
        $this->inventoryModel = $inventoryModel;
        $this->inventoryBatchModel = $inventoryBatchModel;
    }

    public function list()
    {
        return CatchResponse::paginate($this->inventoryModel->getList());
    }

    public function inventoryBatchList()
    {
        return CatchResponse::paginate($this->inventoryBatchModel->getList());
    }
}