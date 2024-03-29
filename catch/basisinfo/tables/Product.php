<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/1/16
 * Time: 11:04
 */

namespace catchAdmin\basisinfo\tables;


use catcher\CatchTable;
use catcher\library\table\Actions;
use catcher\library\table\HeaderItem;
use catcher\library\table\Search;

/**
 * Class Product
 * @package catchAdmin\basisinfo\tables
 */
class Product extends CatchTable
{

    protected function table()
    {
        return $this->getTable('product')
            ->header([
                HeaderItem::label()->selection(),
                HeaderItem::label('序号')->prop('id'),
                HeaderItem::label('产品名称')->prop('product_name'),
                HeaderItem::label('注册证号')->prop('registered_code'),
                HeaderItem::label('备案凭证号')->prop('record_code'),
                HeaderItem::label('生产厂家')->prop('factory_company_name'),
                HeaderItem::label('有效期')->prop('end_time'),
                HeaderItem::label('审核状态')->prop('audit_status_i'),
                HeaderItem::label('审核信息')->prop('audit_info'),
                HeaderItem::label('操作')->width(200)->actions([
                    Actions::update("编辑", "editProduct"),
                    Actions::normal("查看", 'success', "facePrint")->icon('el-icon-printer'),
                ])
            ])
            ->withSearch([
                Search::label('产品名称')->text('company_name', '产品名称'),
                Search::label('有效期')->datetime('factory_type', '有效期'),
                Search::label('状态')->select('status', '请选择状态',
                    Search::options()->add('全部', '')
                        ->add('未审核', 0)
                        ->add('已审核', 1)
                        ->add('审核失败', 2)
                        ->render()
                ),
                Search::hidden('id', '')
            ])
            ->withApiRoute('product')
            ->withActions([
                Actions::normal("新增", 'primary', "addProduct")->icon('el-icon-plus'),
                Actions::normal("审核", 'primary', "audit"),
                Actions::normal("UDI更新", 'primary', "udiUpdate")->icon('el-icon-upload'),
            ])
            ->selectionChange()
            ->render();
    }

    protected function form()
    {

    }
}