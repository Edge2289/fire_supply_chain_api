<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
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
                HeaderItem::label('公司名称')->prop('company_name'),
                HeaderItem::label('公司(英文名)')->prop('company_name_en'),
                HeaderItem::label('营业执照有效期(结束)')->prop('business_end_date_z'),
                HeaderItem::label('厂家类型')->prop('factory_type_name'),
                HeaderItem::label('审核状态')->prop('audit_status_i'),
                HeaderItem::label('审核信息')->prop('audit_info'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update("编辑", "editFactory"),
                    Actions::normal("导出审批", 'success', "facePrint")->icon('el-icon-printer'),
                ])
            ])
            ->withSearch([
                Search::label('公司名称')->text('company_name', '公司名称'),
                Search::label('厂家类型')->text('factory_type', '企业类型'),
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