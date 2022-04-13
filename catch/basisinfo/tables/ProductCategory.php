<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/4/11
 * Time: 19:46
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class ProductCategory
 * @package catchAdmin\basisinfo\tables
 */
class ProductCategory extends CatchTable
{
    public function table()
    {
        return $this->getTable('productCategory')
            ->header([
                HeaderItem::label('名称')->prop('name'),
                HeaderItem::label('权重')->prop('sort'),
                HeaderItem::label('备注')->prop('note'),
                HeaderItem::label('操作')->width(250)->actions([
                    Actions::update(),
                    Actions::delete(),
                ])
            ])
            ->withActions([
                Actions::create()
            ])
            ->withSearch([
                Search::label('名称')->text('name', '名称')->clearable(true),
            ])
            ->withApiRoute('productCategory')
            ->toTreeTable()
            ->render();
    }

    public function form()
    {
        return Factory::create('productCategory');
    }
}