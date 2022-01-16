<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/11
 * Time: 11:38
 */

namespace catchAdmin\basisinfo\controller;


use catchAdmin\basisinfo\model\ProductBasicInfo;
use catcher\base\CatchController;
use catcher\base\CatchRequest;
use catcher\CatchResponse;

/**
 * 产品管理
 *
 * Class Product
 * @package catchAdmin\basisinfo\controller
 */
class Product extends CatchController
{
    private $productBasicInfoModel;

    public function __construct(
        ProductBasicInfo $productBasicInfo
    )
    {
        $this->productBasicInfoModel = $productBasicInfo;
    }

    public function index()
    {
        return CatchResponse::paginate($this->productBasicInfoModel->getList());
    }
}