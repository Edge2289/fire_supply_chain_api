<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/20
 * Time: 20:02
 */

namespace catchAdmin\financial\model;


use catcher\base\CatchModel;
use think\model\relation\HasMany;

/**
 * Class Receivable
 * @package catchAdmin\financial\model
 */
class Receivable extends CatchModel
{
    // 这个根据登陆的账号去获取链接
    protected $connection = 'business';

    protected $name = 'receivable_sheet';

    protected $pk = 'id';

    protected $fieldToTime = ['receivable_time'];

    /**
     * 关联发货单表
     *
     * @return HasMany
     * @author 1131191695@qq.com
     */
    public function manyReceivableSheet(): HasMany
    {
        return $this->hasMany(ReceivableSheet::class, "payment_sheet_id", "id");
    }

    public function getList()
    {
        return $this->catchSearch()->order("id desc")
            ->paginate();
    }

}