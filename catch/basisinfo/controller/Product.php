<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
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
use catchAdmin\basisinfo\request\ProductRecordRequest;
use catchAdmin\basisinfo\request\ProductRegisteredRequest;
use catcher\base\CatchController;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use fire\data\ChangeStatus;
use think\Db;
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
        ProductBasicInfo        $productBasicInfo,
        ProductRecord           $productRecord,
        ProductDistributionInfo $productDistributionInfo,
        ProductSku              $productSku,
        ProductRegistered       $productRegistered
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
        $data = $this->productBasicInfoModel->getList();
        foreach ($data as &$datum) {
            $datum['factory_company_name'] = $datum['withFactory']['company_name'] ?? "";
            $datum['registered_code'] = $datum['withRegistered']['registered_code'] ?? "";
            $datum['end_time'] = $datum['withRegistered']['end_time'] ?? "";
            $datum['record_code'] = $datum['withRecord']['record_code'] ?? "";

            unset($datum['withFactory']);
            unset($datum['withRecord']);
            unset($datum['withRegistered']);
        }

        ChangeStatus::getInstance()->audit()->handle($data);
        return CatchResponse::paginate($data);
    }

    /**
     * 保存
     *
     * @param Request $request
     * @return \think\response\Json
     * @author 1131191695@qq.com
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
     * 更新
     *
     * @param Request $request
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function update(Request $request)
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
     * @param array $map
     * @return bool|int
     * @author 1131191695@qq.com
     */
    protected function BasicInfoCall(array $map)
    {
        $this->validator(ProductBasicInfoRequest::class, $map);

        $skuData = $map['skuData'] ?? [];
        unset($map['skuData']);
        $skuIds = [];
        $addSkus = [];
        $updateSkus = [];
        if ($skuData) {
            // 如果存在sku信息
            foreach ($skuData as $datum) {
                $datum['valid_start_time'] = strtotime($datum['valid_start_time']);
                $datum['valid_end_time'] = strtotime($datum['valid_end_time']);
                $datum['updated_at'] = time();
                if (!empty($datum['id'])) {
                    $skuIds[] = $datum['id'];
                    $updateSkus[] = $datum;
                } else {
                    $datum['product_code'] = getCode("PC");
                    $datum['created_at'] = time();
                    $addSkus[] = $datum;
                }
            }
        }

        app(Db::class)->startTrans();
        try {
            if (isset($map['id']) && !empty($map['id'])) {
                $data = $this->productBasicInfoModel->where('id', $map['id'])->find();
                if (!$data) {
                    throw new BusinessException("数据不存在");
                }
                $id = $this->productBasicInfoModel->updateBy($map['id'], $map);
                if (!$id) {
                    throw new \Exception("修改失败");
                }
                $id = $map['id'];
            } else {
                $id = $this->productBasicInfoModel->storeBy($map);
            }
            if ($skuData) {
                // 删除
                foreach ($this->productSku->whereNotIn('id', $skuIds)->select() as $item) {
                    $item->delete();
                }
                if (!empty($addSkus)) {
                    // 新增
                    foreach ($addSkus as &$datum) {
                        $datum['product_id'] = $id;
                    }
                    $this->productSku->insertAllBy($addSkus);
                }
                if (!empty($updateSkus)) {
                    // 更新
                    foreach ($updateSkus as $updateSku) {
                        $this->productSku->updateBy($updateSku['id'], $updateSku);
                    }
                }
            }
        } catch (\Exception $exception) {
            app(Db::class)->rollback();
            throw new BusinessException(sprintf("操作失败【%s】", $exception->getMessage()));
        }
        app(Db::class)->commit();
        return $id;
    }

    /**
     * 注册证信息
     *
     * @param array $map
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    protected function registeredCall(array $map)
    {
        $map['end_time'] = strtotime($map['end_time']);
        $map['registered_time'] = strtotime($map['registered_time']);

        $this->validator(ProductRegisteredRequest::class, $map);

        if (isset($map['id']) && !empty($map['id'])) {
            $data = $this->productRegistered->where('id', $map['id'])->find();
            if (!$data) {
                throw new BusinessException("数据不存在");
            }
            return $this->productRegistered->updateBy($map['id'], $map);
        } else {
            return $this->productRegistered->storeBy($map);
        }
    }

    /**
     * 备案信息
     *
     * @param array $map
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    protected function recordCall(array $map)
    {
        $map['record_time'] = strtotime($map['record_time']);
        $this->validator(ProductRecordRequest::class, $map);
        if (isset($map['id']) && !empty($map['id'])) {
            $data = $this->productRecord->where('id', $map['id'])->find();
            if (!$data) {
                throw new BusinessException("数据不存在");
            }
            return $this->productRecord->updateBy($map['id'], $map);
        } else {
            return $this->productRecord->storeBy($map);
        }
    }

    /**
     * 经销商信息
     *
     * @param array $map
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    protected function distributionInfoCall(array $map)
    {
        $map['signing_date'] = empty($map['signing_date']) ? $map['signing_date'] : strtotime($map['signing_date']);
        $map['end_time'] = empty($map['end_time']) ? $map['end_time'] : strtotime($map['end_time']);
        if (empty($map['product_id']) || empty($map['payment_days'])) {
            throw new BusinessException("请填写完整信息");
        }
        if (isset($map['id']) && !empty($map['id'])) {
            $data = $this->productDistributionInfo->where('id', $map['id'])->find();
            if (!$data) {
                throw new BusinessException("数据不存在");
            }
            return $this->productDistributionInfo->updateBy($map['id'], $map);
        } else {
            return $this->productDistributionInfo->storeBy($map);
        }
    }

    /**
     * 改变
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
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
                "name" => "经销信息",
                "component" => "distribution_info",
            ];
            $productData['distribution_info'] = $this->productDistributionInfo->where("product_id", $id)->find();
            $productData['sku_data'] = $this->productSku->where("product_id", $id)->select();
        }
        return CatchResponse::success([
            'componentData' => $map,
            'productData' => $productData,
        ]);
    }

    /**
     * 审核
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 1131191695@qq.com
     */
    public function audit(Request $request)
    {
        $data = $request->param();
        $productData = $this->productBasicInfoModel->findBy($data['id']);
        if (empty($productData)) {
            throw new BusinessException("不存在产品");
        }
        $b = $this->productBasicInfoModel->updateBy($data['id'], [
            'audit_status' => $data['audit_status'],
            'audit_info' => $data['audit_info'],
            'audit_user_id' => request()->user()->id,
            'audit_user_name' => request()->user()->username,
        ]);
        if ($b) {
            return CatchResponse::success();
        }
        return CatchResponse::fail("操作失败");
    }

    /**
     * 获取sku列表
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     * @author 1131191695@qq.com
     */
    public function skuList(Request $request)
    {
        $data = $request->param();
        // 搜索条件 时间
        $data = $this->productSku->leftJoin("product_basic_info pbi", "pbi.id = f_product_sku.product_id")
            ->where("pbi.audit_status", 1) // 已审核的
            ->when(!empty($data), function ($query) use ($data) {
                if (!empty($data['product_name'])) {
                    $query->where("pbi.product_name", "like", "%" . $data['product_name'] . "%");
                }
                if (!empty($data['sku_code'])) {
                    $query->where("f_product_sku.sku_code", "like", "%" . $data['sku_code'] . "%");
                }
            })
            ->field([
                "f_product_sku.id", "f_product_sku.product_id", "f_product_sku.product_code",
                "f_product_sku.sku_code", "f_product_sku.item_number", "f_product_sku.unit_price",
                "f_product_sku.tax_rate", "f_product_sku.n_tax_price", "f_product_sku.packing_size",
                "f_product_sku.packing_specification", "pbi.product_name", "f_product_sku.udi",
                "f_product_sku.entity"
            ])
            ->paginate();
        return CatchResponse::paginate($data);
    }
}