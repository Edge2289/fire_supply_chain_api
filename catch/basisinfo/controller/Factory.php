<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 11:49
 */

namespace catchAdmin\basisinfo\controller;


use catcher\base\CatchController;
use catcher\CatchResponse;
use \catchAdmin\basisinfo\model\Factory as FactoryModel;

/**
 * 厂家管理
 *
 * Class Factory
 * @package catchAdmin\basisinfo\controller
 */
class Factory extends CatchController
{
    public $factory;

    public function __construct(FactoryModel $factory)
    {
        $this->factory = $factory;
    }

    /**
     * 厂家列表
     *
     * @author xiejiaqing
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        return CatchResponse::paginate($this->factory->getList());
    }

    public function save()
    {

    }

    public function update()
    {

    }

    public function audit()
    {

    }

    public function delete()
    {

    }
}