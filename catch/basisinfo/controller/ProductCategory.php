<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/4/11
 * Time: 17:14
 */

namespace catchAdmin\basisinfo\controller;


use app\Request;
use catcher\base\CatchController;
use catchAdmin\basisinfo\model\ProductCategory as ProductCategoryModel;
use catcher\CatchResponse;

/**
 * Class ProductCategory
 * @package catchAdmin\basisinfo\controller
 */
class ProductCategory extends CatchController
{
    private $productCategoryModel;

    public function __construct(ProductCategoryModel $productCategoryModel)
    {
        $this->productCategoryModel = $productCategoryModel;
    }

    public function index()
    {
        $data = $this->productCategoryModel->select()->toTree(0, 'p_id');
        return CatchResponse::success($data);
    }

    public function save(Request $request)
    {
        $data = $request->param();
        $data['p_id'] = $data['p_id'][0] ?? 0;
        $this->productCategoryModel->save($data);
        return CatchResponse::success();
    }

    public function update()
    {

    }

    public function delete()
    {

    }
}