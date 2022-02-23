<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/2/6
 * Time: 21:05
 */

namespace catchAdmin\purchase\tables\forms;


use catchAdmin\basisinfo\model\SupplierLicense;
use catchAdmin\permissions\model\Users;
use catcher\library\form\Form;

/**
 * Class ChangePurchase
 * @package catchAdmin\purchase\tables\forms
 */
class ChangePurchase extends Form
{
    private $users;
    private $supplier;

    public function __construct(
        Users           $users,
        SupplierLicense $supplier
    )
    {
        $this->users = $users;
        $this->supplier = $supplier;
    }

    public function fields(): array
    {
        return [
            self::date("purchase_date", "采购日期")->col(12)->required(),
            self::select("user_id", "采购人员")
                ->options(
                // 获取自身公司下的员工
                    $this->getUser()
                )->col(12)->required(),
            self::select("supplier_id", "供应商")
                ->options(
                    $this->supplier->getSupplier()
                )->col(12)->required(),
            self::select("settlement_status", "结算类型")
                ->options(
                    self::options()->add('现结', "0")
                        ->add('月结', "1")->render()
                )->col(12)->required(),
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