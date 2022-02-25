<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/8
 * Time: 22:32
 */

namespace catcher\exceptions;


use catcher\Code;

/**
 * Class BusinessException
 * @package catcher\exceptions
 */
class BusinessException extends CatchException
{
    protected $code = Code::FAILED;
}