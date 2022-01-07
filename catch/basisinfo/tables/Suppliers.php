<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/6
 * Time: 19:49
 */

namespace catchAdmin\basisinfo\tables;


use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class Suppliers
 * @package catchAdmin\basisinfo\tables
 */
class Suppliers extends CatchTable
{

    public function table()
    {
        return $this->getTable('suppliers')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('编号')->prop('id'),
                HeaderItem::label('统一社会信用代码')->prop('unified_code'),
                HeaderItem::label('企业名称')->prop('company_name'),
                HeaderItem::label('企业类型')->prop('company_type'),
                HeaderItem::label('法人')->prop('legal_person'),
                HeaderItem::label('经营范围')->prop('business_scope'),
                HeaderItem::label('登记日期')->prop('establish_date'),
                HeaderItem::label('操作')->width(200)->actions([
//                    Actions::normal()
                ])
            ])
            ->withSearch([
                Search::label('企业名称')->text('company_name', '企业名称'),
                Search::label('企业类型')->text('email', '企业类型'),
                Search::label('法人')->text('legal_person', '法人'),
                Search::hidden('id', '')
            ])
            ->withApiRoute('users')
            ->withActions([
//                Actions::create(),
                Actions::normal("面单打印", 'danger', "facePrint")->icon('el-icon-view'),
//                Actions::export()
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
//        return Factory::create('user');
    }
}