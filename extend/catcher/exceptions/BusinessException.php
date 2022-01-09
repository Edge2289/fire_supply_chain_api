<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
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