<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/9
 * Time: 20:59
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
use catcher\CatchTable;

/**
 * Class QualificationFile
 * @package catchAdmin\basisinfo\tables
 */
class QualificationFile extends CatchTable
{

    protected function table()
    {
        return $this->getTable('qualificationFile')
            ->withApiRoute('basisinfo')
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
        return Factory::create('qualificationFile');
    }
}