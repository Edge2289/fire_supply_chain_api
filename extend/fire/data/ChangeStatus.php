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
 * @package fire\data
 */
class ChangeStatus
{
    private static $auditStatus = false;

    private static $status = false;

    private $statusArr = [
        "未完成", "已完成", "作废"
    ];
    //审核状态 {0:未审核,1:已审核,2:审核失败}
    private $auditStatusI = [
        "未审核", "已审核", "审核失败"
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
            if (self::$auditStatus) {
                $datum['audit_status_i'] = $this->auditStatusI[$datum['audit_status']];
            }
            if (self::$status) {
                $datum['status_i'] = $this->statusArr[$datum['status']];
            }
        }
    }

    public function audit(array $arr = []): self
    {
        if (!empty($arr)) {
            $this->statusArr = $arr;
        }
        self::$auditStatus = true;
        return $this;
    }

    public function status(array $arr = []): self
    {
        if (!empty($arr)) {
            $this->statusArr = $arr;
        }
        self::$status = true;
        return $this;
    }

}