<?php

namespace catchAdmin\system\controller;

use app\Request;
use catcher\base\CatchController;
use catchAdmin\system\model\Config as ConfigModel;
use catcher\CatchResponse;
use think\response\Json;

class Config extends CatchController
{
    protected $configModel;

    public function __construct(ConfigModel $configModel)
    {
        $this->configModel = $configModel;
    }

    /**
     * 获取父级别配置
     *
     * @time 2020年04月17日
     * @return Json
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     */
    public function parent()
    {
        return CatchResponse::success($this->configModel->getParentConfig());
    }

    /**
     * 存储配置
     *
     * @time 2020年04月17日
     * @param Request $request
     * @return Json
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     */
    public function save(Request $request)
    {
        return CatchResponse::success([
            'id' => $this->configModel->storeBy($request->param()),
            'parents' => $this->configModel->getParentConfig(),
        ]);
    }

    /**
     * 获取配置
     *
     * @time 2020年04月20日
     * @param $id
     * @return Json
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     */
    public function read($id)
    {
        return CatchResponse::success($this->configModel->getConfig($id));
    }

    /**
     * @param $parent
     * @param $key
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function getSeparateConfig($parent, $key)
    {
        if ($key) {
            $parent .= ('.' . $key);
        }
        if (empty($parent)) {
            return CatchResponse::fail();
        }
        return CatchResponse::success($this->configModel->where("key", $parent)->find());
    }
}