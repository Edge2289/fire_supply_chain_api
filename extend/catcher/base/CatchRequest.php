<?php
declare(strict_types=1);

namespace catcher\base;

use app\Request;
use catcher\exceptions\FailedException;
use catcher\exceptions\ValidateFailedException;
use catcher\Utils;

class CatchRequest extends Request
{
  /**
   * @var bool
   */
    protected $needCreatorId = true;
    /**
     *  批量验证
     *
     * @var bool
     */
    protected $batch = false;

    /**
     * 检查的参数
     *
     * @var array
     */
    protected $param = [];


    /**
     * Request constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->validate();
    }

    /**
     * 初始化验证
     *
     * @time 2019年11月27日
     * @throws \Exception
     * @return mixed
     */
    protected function validate()
    {
        if (method_exists($this, 'rules')) {
          try {
            $validate = app('validate');
            // 批量验证
            if ($this->batch) {
              $validate->batch($this->batch);
            }

            // 验证
            $message = [];
            if (method_exists($this, 'message')) {
                $message = $this->message();
            }
            // 如果没有设置参数，则从请求头获取
            if (empty($this->param)) {
                $this->param = request()->param();
            }
            if (!$validate->message(empty($message) ? [] : $message)->check($this->param, $this->rules())) {
              throw new FailedException($validate->getError());
            }
          } catch (\Exception $e) {
            throw new ValidateFailedException($e->getMessage());
          }
        }

        // 设置默认参数
        if ($this->needCreatorId) {
            $this->param['creator_id'] = $this->user()->id;
        }

        return true;
    }

    /**
     * rewrite post
     *
     * @time 2020年10月15日
     * @param string $name
     * @param null $default
     * @param string $filter
     * @return array|mixed|null
     */
    public function post($name = '', $default = null, $filter = '')
    {
        if ($this->needCreatorId) {
            $this->post['creator_id'] = $this->user()->id;
        }

        return parent::post($name, $default, $filter); // TODO: Change the autogenerated stub
    }

    /**
     * 过滤空字段
     *
     * @time 2021年01月16日
     * @return $this
     */
    public function filterEmptyField(): CatchRequest
    {
        if ($this->isGet()) {
            $this->get = Utils::filterEmptyValue($this->get);
        } elseif ($this->isPost()) {
            $this->post =  Utils::filterEmptyValue($this->post);
        } else {
            $this->put =  Utils::filterEmptyValue($this->put);
        }

        return $this;
    }
}
