<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/6
 * Time: 21:05
 */

namespace catchAdmin\purchase\tables\forms;


use catchAdmin\basisinfo\model\SupplierLicense;
use catchAdmin\inventory\model\Warehouse;
use catchAdmin\permissions\model\Users;
use catcher\library\form\Form;

/**
 * Class ChangeProcurementWarehousing
 * @package catchAdmin\purchase\tables\forms
 */
class ChangeProcurementWarehousing extends Form
{
    private $users;
    private $warehouse;

    public function __construct(
        Users     $users,
        Warehouse $warehouse
    )
    {
        $this->users = $users;
        $this->warehouse = $warehouse;
    }

    public function fields(): array
    {
        return [
            self::date("purchase_date", "入库日期")->col(12)->required(),
            self::select("user_id", "入库人员")
                ->options(
                // 获取自身公司下的员工
                    $this->getUser()
                )->col(12)->required(),
            self::select("supplier_id", "仓库")
                ->options(
                    $this->warehouse->tableGetWarehouse()
                )->
                col(12)->required(),
            self::input("delivery_code", "收货单号")->col(12)->required(),
            self::textarea("remark", "备注")->col(12)->required(),
        ];
    }

    public function getUser()
    {
        $userId = request()->user()->id;
        $data = $this->users->where("id", $userId)->find();
        if (!$data['department_id']) {
            return [];
        }
        $data = $this->users->where("department_id", $data['department_id'])->select();
        $map = [];
        foreach ($data as $datum) {
            $map[] = [
                'value' => (string)$datum['id'],
                'label' => $datum['username'],
            ];
        }
        return $map;
    }
}