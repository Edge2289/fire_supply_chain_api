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
use catchAdmin\basisinfo\model\ProductBasicInfo;
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

    /**
     * 列表查询
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(Request $request)
    {
        $data = $request->param();

        $data = $this->productCategoryModel->when(isset($data['name']) && !empty($data['name']), function ($query) use ($data) {
            $query->where("name", 'like', "%" . $data['name'] . "%");
        })->select()->toTree(0, 'p_id');
        return CatchResponse::success($data);
    }

    public function categoryList()
    {
        $data = $this->productCategoryModel->select()->order("sort", "desc")->toTree(0, 'p_id');
        return CatchResponse::success($data);
    }

    public function save(Request $request)
    {
        $data = $request->param();
        $data['p_id'] = $data['p_id'][0] ?? 0;
        $data['sort'] = empty($data['sort']) ? 0 : $data['sort'];
        $this->productCategoryModel->save($data);
        return CatchResponse::success();
    }

    /**
     * 更新
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update(Request $request)
    {
        $data = $request->param();
        $data['p_id'] = $data['p_id'][0] ?? 0;
        $this->productCategoryModel->updateBy($data['id'], $data);
        return CatchResponse::success();
    }

    /**
     * 删除
     *
     * @param Request $request
     * @return \think\response\Json
     */
    public function delete(Request $request)
    {
        $data = $request->param();
        $d = $this->productCategoryModel->where('p_id', $data['id'])->count();
        if ($d != 0) {
            return CatchResponse::fail("存在子级无法删除");
        }
        if (ProductBasicInfo::where('product_category_id', $data['id'])->count() != 0) {
            return CatchResponse::fail("存在商品使用无法删除");
        }
        $this->productCategoryModel->destroy(['id' => $data['id']]);
        return CatchResponse::success();
    }
}