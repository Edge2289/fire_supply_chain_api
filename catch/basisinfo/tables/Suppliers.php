<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/6
 * Time: 19:49
 */

namespace catchAdmin\basisinfo\tables;


use catchAdmin\basisinfo\tables\forms\Factory;
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
//                HeaderItem::label('企业类型')->prop('company_type'),
                HeaderItem::label('法人')->prop('legal_person'),
//                HeaderItem::label('经营范围')->prop('business_scope'),
                HeaderItem::label('登记日期')->prop('establish_date'),
                HeaderItem::label('审核状态')->prop('audit_status_i'),
                HeaderItem::label('状态')->prop('status_i'),
                HeaderItem::label('审核信息')->prop('audit_info'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update("编辑", "editSuppliers"),
                    Actions::normal("查看", 'success', "facePrint")->icon('el-icon-printer'),
                ])
            ])
            ->withSearch([
                Search::label('企业名称')->text('company_name', '企业名称'),
                Search::label('企业类型')->text('email', '企业类型'),
                Search::label('法人')->text('legal_person', '法人'),
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
            ->withApiRoute('suppliers')
            ->withActions([
                Actions::normal("新增", 'primary', "addSuppliers")->icon('el-icon-plus'),
                Actions::normal("审核", 'primary', "audit")->icon('el-icon-bangzhu'),
                Actions::normal("启动", 'primary', "open")->icon('el-icon-success'),
                Actions::normal("停用", 'warning', "disabled")->icon('el-icon-error'),
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {

    }
}