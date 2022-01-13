<?php
/**
 * Created by PhpStorm.
 * author: xiejiaqing
 * Note: Tired as a dog
 * Date: 2022/1/12
 * Time: 21:14
 */

namespace catchAdmin\basisinfo\tables;


use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class Factory
 * @package catchAdmin\basisinfo\tables
 */
class Factory extends CatchTable
{

    protected function table()
    {
        return $this->getTable('factory')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('编号')->prop('id'),
                HeaderItem::label('厂家编号')->prop('factory_code'),
                HeaderItem::label('厂家名称')->prop('factory_name'),
                HeaderItem::label('厂家名称(英文名)')->prop('factory_name_en'),
                HeaderItem::label('营业执照有效期')->prop('business_date'),
                HeaderItem::label('厂家类型')->prop('factory_type'),
                HeaderItem::label('审核状态')->prop('audit_status'),
                HeaderItem::label('状态')->prop('status'),
                HeaderItem::label('审核信息')->prop('audit_info'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update(),
                    Actions::normal("导出审批", 'success', "facePrint")->icon('el-icon-printer'),
                ])
            ])
            ->withSearch([
                Search::label('厂家名称')->text('factory_name', '厂家名称'),
                Search::label('厂家类型')->text('email', '企业类型'),
                Search::label('状态')->select('status', '请选择供应商状态',
                    Search::options()->add('全部', '')
                        ->add('停用', 0)
                        ->add('启用', 1)
                        ->render()
                ),
                Search::label('审核状态')->select('status', '请选择审核状态',
                    Search::options()->add('全部', '')
                        ->add('未审核', 0)
                        ->add('已审核', 1)
                        ->add('审核失败', 2)
                        ->render()
                ),
                Search::hidden('id', '')
            ])
            ->withApiRoute('factory')
            ->withActions([
                Actions::normal("新增", 'primary', "addFactory")->icon('el-icon-plus'),
                Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu'),
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {
        // TODO: Implement form() method.
    }
}