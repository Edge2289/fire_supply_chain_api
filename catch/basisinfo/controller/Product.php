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
use catchAdmin\basisinfo\model\ProductDistributionInfo;
use catchAdmin\basisinfo\model\ProductRecord;
use catchAdmin\basisinfo\model\ProductRegistered;
use catchAdmin\basisinfo\model\ProductSku;
use catchAdmin\basisinfo\request\ProductBasicInfoRequest;
use catcher\base\CatchController;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use think\Request;

/**
 * 产品管理
 *
 * Class Product
 * @package catchAdmin\basisinfo\controller
 */
class Product extends CatchController
{
    private $productBasicInfoModel;
    private $productRecord;
    private $productDistributionInfo;
    private $productSku;
    private $productRegistered;


    public function __construct(
        ProductBasicInfo $productBasicInfo,
        ProductRecord $productRecord,
        ProductDistributionInfo $productDistributionInfo,
        ProductSku $productSku,
        ProductRegistered $productRegistered
    )
    {
        $this->productBasicInfoModel = $productBasicInfo;
        $this->productRecord = $productRecord;
        $this->productDistributionInfo = $productDistributionInfo;
        $this->productSku = $productSku;
        $this->productRegistered = $productRegistered;
    }

    public function index()
    {
        return CatchResponse::paginate($this->productBasicInfoModel->getList());
    }

    /**
     * 保存
     *
     * @author xiejiaqing
     * @param Request $request
     * @return \think\response\Json
     */
    public function save(Request $request)
    {
        $type = $request->param('form_product_type') ?? "";
        if (empty($type)) {
            return CatchResponse::fail("请求类型缺失");
        }
        $result = $this->{$type}($request->param());
        if ($result) {
            return CatchResponse::success([
                'id' => $result
            ]);
        }
        return CatchResponse::fail();
    }

    /**
     * 基础数据保存
     *
     * @author xiejiaqing
     * @param array $map
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function BasicInfoCall(array $map)
    {
        $this->validator(ProductBasicInfoRequest::class, $map);

        if (isset($map['id']) && !empty($map['id'])) {
            $data = $this->productBasicInfoModel->where('id', $map['id'])->find();
            if (!$data) {
                throw new BusinessException("数据不存在");
            }
            return $this->productBasicInfoModel->updateBy($map['id'], $map);
        } else {
            return $this->productBasicInfoModel->storeBy($map);
        }
    }

    public function update()
    {

    }

    /**
     * 改变
     *
     * @param Request $request
     * @return \think\response\Json
     * @author xiejiaqing
     */
    public function changeProductSetting(Request $request)
    {
        $id = $request->param('id') ?? 0;
        $map = [
            [
                "id" => 1,
                "name" => "基础信息",
                "component" => "basic_info",
            ]
        ];
        $productData = [];
        if ($id != 0) {
            $data = $this->productBasicInfoModel->findBy($id);
            if (empty($data)) {
                throw new BusinessException("找不到数据");
            }
            $productData['basic_info'] = $data;
            $data['data_maintenance'] = (int)$data['data_maintenance'];
            if ($data['data_maintenance'] == 1) {
                // 注册证
                $map[] = [
                    "id" => 2,
                    "name" => "注册证信息",
                    "component" => "registered",
                ];
                $productData['registered'] = $this->productRegistered->where("product_id", $id)->find();
            } else {
                // 备案
                $map[] = [
                    "id" => 3,
                    "name" => "备案信息",
                    "component" => "record",
                ];
                $productData['record'] = $this->productRecord->where("product_id", $id)->find();
            }
            $map[] = [
                "id" => 4,
                "name" => "经销商信息",
                "component" => "distribution_info",
            ];
            $productData['distribution_info'] = $this->productDistributionInfo->where("product_id", $id)->find();
        }
        return CatchResponse::success([
            'componentData' => $map,
            'productData' => $productData,
        ]);
    }
}