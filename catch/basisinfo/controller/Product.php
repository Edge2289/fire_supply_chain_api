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
use catchAdmin\basisinfo\model\ProductQualification;
use catchAdmin\basisinfo\model\ProductEntity;
use catchAdmin\basisinfo\model\ProductRecord;
use catchAdmin\basisinfo\model\ProductRegistered;
use catchAdmin\basisinfo\model\ProductSku;
use catchAdmin\basisinfo\model\ProductUdi;
use catchAdmin\basisinfo\request\ProductBasicInfoRequest;
use catchAdmin\basisinfo\request\ProductRecordRequest;
use catchAdmin\basisinfo\request\ProductRegisteredRequest;
use catcher\base\CatchController;
use catcher\CatchResponse;
use catcher\exceptions\BusinessException;
use catcher\Utils;
use fire\data\ChangeStatus;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use \think\facade\Db;
use think\facade\Filesystem;
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
    private $productQualification;
    private $productSku;
    private $productRegistered;
    private $productEntity;


    public function __construct(
        ProductBasicInfo     $productBasicInfo,
        ProductRecord        $productRecord,
        ProductQualification $productQualification,
        ProductSku           $productSku,
        ProductRegistered    $productRegistered,
        ProductEntity        $productEntity
    )
    {
        $this->productBasicInfoModel = $productBasicInfo;
        $this->productRecord = $productRecord;
        $this->productQualification = $productQualification;
        $this->productSku = $productSku;
        $this->productRegistered = $productRegistered;
        $this->productEntity = $productEntity;
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
        $map['product_category'] = $map['product_category'][0] ?? 0;

        $this->productBasicInfoModel->startTrans();
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
                // 清除数据
                Db::table("f_product_sku")->where("product_id", $id)->delete();
                Db::table("f_product_entity")->where("product_id", $id)->delete();
            } else {
                $id = $this->productBasicInfoModel->storeBy($map);
            }
            foreach ($skuData as $skuDatum) {
                if (empty($skuDatum['entity'])) {
                    throw new BusinessException("" . $skuDatum['udi'] . "下的单位为空");
                }
                // entityOptions
                if (!isset($skuDatum['product_code']) || empty($skuDatum['product_code'])) {
                    $skuDatum['product_code'] = getCode("PC");
                }
                $skuDatum['product_id'] = $id;
                $entityOptions = json_decode($skuDatum['entity'], true);
                unset($skuDatum['entity']);
                unset($skuDatum['id'], $skuDatum['created_at'], $skuDatum['updated_at'], $skuDatum['deleted_at']);
                $skuId = $this->productSku->insertGetId($skuDatum);
                $map = [];
                foreach ($entityOptions as $value) {
                    $map['product_id'] = $id;
                    $map['product_sku_id'] = $skuId;
                    $map['deputy_unit_name'] = $value['deputyUnitName'];
                    $map['proportion'] = $value['proportion'];
                    $this->productEntity->insert($map);
                }
            }
        } catch (\Exception $exception) {
            $this->productBasicInfoModel->rollback();
            throw new BusinessException(sprintf("操作失败【%s】", $exception->getMessage()));
        }
        $this->productBasicInfoModel->commit();
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
    protected function qualificationCall(array $map)
    {
        foreach ($map as $key => $value) {
            if (in_array($key, [
                    'imported_documents_url', 'product_appearance_url', 'product_quality_url',
                    'product_register_url', 'report_delivery_url'
                ]) && !empty($value)) {
                $map[$key] = $value[0] ?? "";
            }
        }
        if (isset($map['id']) && !empty($map['id'])) {
            $data = $this->productQualification->where('id', $map['id'])->find();
            if (!$data) {
                throw new BusinessException("数据不存在");
            }
            return $this->productQualification->updateBy($map['id'], $map);
        } else {
            return $this->productQualification->storeBy($map);
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
            }
            if ($data['data_maintenance'] == 2) {
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
                "name" => "资质审核",
                "component" => "qualification",
            ];
            $qualificationHandle = function () use ($id) {
                $data = $this->productQualification->where("product_id", $id)->find();
                if (empty($data)) {
                    return "";
                }
                $map = [];
                foreach ($data->toArray() as $key => $value) {
                    if (in_array($key, [
                            'imported_documents_url', 'product_appearance_url', 'product_quality_url',
                            'product_register_url', 'report_delivery_url'
                        ]) && !empty($value)) {
                        $map[$key] = [$value];
                    } else {
                        $map[$key] = $value;
                    }
                }
                return $map;
            };
            $productData['qualification'] = $qualificationHandle();
            $productData['sku_data'] = $this->productSku->with([
                "hasProductEntity" => function ($query) {
                    $query->field(["product_sku_id", "proportion", "deputy_unit_name as deputyUnitName"]);
                }
            ])->where("product_id", $id)->select();
            foreach ($productData['sku_data'] as &$sku_datum) {
                $sku_datum['entityOptions'] = $sku_datum['hasProductEntity'];
                $entity = "";
                foreach ($sku_datum['hasProductEntity'] as $k => $value) {
                    if ($k == 0) {
                        $entity = $value['deputyUnitName'];
                        continue;
                    }
                    $entity .= ("/(" . $value['deputyUnitName'] . "=" . $value['proportion'] . ")");
                }
                $sku_datum['entity'] = $entity;
                unset($sku_datum['hasProductEntity']);
            }
        }
        if (isset($productData['basic_info'])) {
            $productData['basic_info']['product_category'] = [(int)$productData['basic_info']['product_category']];
        }
        return CatchResponse::success([
            'componentData' => $map,
            'initialComponentData' => [
                [
                    "id" => 1,
                    "name" => "基础信息",
                    "component" => "basic_info",
                ], [
                    "id" => 2,
                    "name" => "注册证信息",
                    "component" => "registered",
                ], [
                    "id" => 3,
                    "name" => "备案信息",
                    "component" => "record",
                ], [
                    "id" => 4,
                    "name" => "资质审核",
                    "component" => "qualification",
                ]
            ],
            'productData' => $productData,
            'defaultTaxRate' => Utils::config('product.tax'),
        ]);
    }

    public function delete()
    {
        return CatchResponse::fail("不允许删除");
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

    /**
     * 上传udi文件
     *
     * @return \think\response\Json
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @author 1131191695@qq.com
     */
    public function udi()
    {
        $file = request()->file('file');
        $name = Filesystem::putFile('file', $file);
        $fileExtendName = substr(strrchr($name, '.'), 1);
        if (!in_array($fileExtendName, ['xls', 'xlsx'])) {
            return json(['code' => 0, 'message' => '文件格式不对']);
        }
        if ($fileExtendName == 'xls') {
            $objReader = IOFactory::createReader('Xls');
        } else {
            $objReader = IOFactory::createReader('Xlsx');
        }
        $path = Filesystem::disk('public')->putFile("", $file, 'md5');
        $objPHPExcel = $objReader->load(root_path() . "public/storage/" . $path);
        $sheet = $objPHPExcel->getSheet(0);   //excel中的第一张sheet

        $highestRow = $sheet->getHighestRow();       // 取得总行数
        $highestColumn = $sheet->getHighestColumn();   // 取得总列数
        Coordinate::columnIndexFromString($highestColumn);
        $lines = $highestRow - 1;
        if ($lines <= 0) {
            return json(['code' => 0, 'message' => '保存失败']);
        }
        $data = array();
        for ($j = 2; $j <= $highestRow; $j++) {
            $data[$j - 2] = [
                'udi' => trim($objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue()),
                'product_name' => trim($objPHPExcel->getActiveSheet()->getCell("I" . $j)->getValue()),
                'item_number' => trim($objPHPExcel->getActiveSheet()->getCell("K" . $j)->getValue()),
                'manufacturer' => trim($objPHPExcel->getActiveSheet()->getCell("R" . $j)->getValue()),
                'registered' => trim($objPHPExcel->getActiveSheet()->getCell("U" . $j)->getValue()),
            ];
        }
        app(ProductUdi::class)->insertAll($data);
        return CatchResponse::success([], 'success', 200);
    }

    /**
     * @return \think\response\Json
     * @author 1131191695@qq.com
     */
    public function udiList()
    {
        return CatchResponse::paginate(app(ProductUdi::class)->getList());
    }
}