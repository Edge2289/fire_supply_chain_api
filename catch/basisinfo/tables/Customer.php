<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/2/13
 * Time: 21:01
 */

namespace catchAdmin\basisinfo\tables;


use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class Customer
 * @package catchAdmin\basisinfo\tables
 */
class Customer extends CatchTable
{

    protected function table()
    {
        return $this->getTable('customer')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('编号')->prop('id'),
                HeaderItem::label('客户类型')->prop('customer_type'),
                HeaderItem::label('公司名称')->prop('company_name'),
                HeaderItem::label('有效期(结束)')->prop('effective_end_date'),
                HeaderItem::label('法人')->prop('legal_person'),
                HeaderItem::label('审核状态')->prop('audit_status_i'),
                HeaderItem::label('审核信息')->prop('audit_info'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update("编辑", "edit"),
                    Actions::normal("导出审批", 'success', "facePrint")->icon('el-icon-printer'),
                ])
            ])
            ->withSearch([
                Search::label('客户类型')->select('customer_type', '客户类型',
                    Search::options()->add('全部', '')
                        ->add('经销商', 1)
                        ->add('医院', 2)
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
            ->withApiRoute('customer')
            ->withActions([
                Actions::normal("新增", 'primary', "add")->icon('el-icon-plus'),
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