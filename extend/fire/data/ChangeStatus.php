<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/3/26
 * Time: 11:17
 */

namespace fire\data;


/**
 * Class ChangeStatus
 * @method self settlementStatus
 * @method self status
 * @method self invoiceStatus
 * @method self settlementType
 * @method self auditStatus
 * @package fire\data
 */
class ChangeStatus
{

    private $statusI = [
        "未完成", "已完成", "作废"
    ];
    //审核状态 {0:未审核,1:已审核,2:审核失败}
    private $auditStatusI = [
        "未审核", "已审核", "审核失败"
    ];
    // 结款状态 {0:未结款,1:部分结款,2:已付清}
    private $settlementStatusI = [
        "未结款", "部分结款", "已付清"
    ];

    /**
     * @return ChangeStatus
     * @author 1131191695@qq.com
     */
    public static function getInstance(): self
    {
        return new self();
    }

    /**
     * @param $data
     * @author 1131191695@qq.com
     */
    public function handle(&$data)
    {
        foreach ($data as &$datum) {
            foreach ($this->getStatusMap() as $statusDatum) {
                if ($this->{$statusDatum} ?? false) {
                    $name = $statusDatum . "I";
                    // 不存在
                    if (!($this->{$name} ?? false)) {
                        continue;
                    }
                    $datum[uncamelize($name)] = $this->{$name}[$datum['audit_status']];
                }
            }
        }
    }

    /**
     * 获取状态的集合
     *
     * @return array
     */
    public function getStatusMap(): array
    {
        $r = new \ReflectionClass($this);
        $statusMap = [];
        foreach (explode("method", $r->getDocComment()) as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $name = explode("\n", $value)[0] ?? "";
            $name = substr($name, 6);
            !empty(trim($name)) && $statusMap[] = trim($name);
        }
        return $statusMap;
    }

    /**
     * 魔术方法
     *
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (!empty($arguments)) {
            $key = $name . "I";
            $this->{$key} = $arguments[0];
        }
        $this->$name = true;
        return $this;
    }

}