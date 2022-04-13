<?php
/**
 * Created by PhpStorm.
 * author: 1131191695@qq.com
 * Note: Tired as a dog
 * Date: 2022/4/11
 * Time: 19:46
 */

namespace catchAdmin\basisinfo\tables\forms;


use catcher\library\form\Form;
use catchAdmin\basisinfo\model\ProductCategory as ProductCategoryModel;

/**
 * Class ProductCategory
 * @package catchAdmin\basisinfo\tables\forms
 */
class ProductCategory extends Form
{
    public function fields(): array
    {
        return [
            self::input('name', '名称')->required(),
            self::cascader('p_id', '上级分类', [])->options(
                ProductCategoryModel::field(['id', 'name', 'p_id'])
                    ->select()->toTree(0, 'p_id')
            )->col(12)->props(self::props('name', 'id', [
                'checkStrictly' => true
            ]))->filterable(true)->clearable(true)->style(['width' => '100%']),

            self::input('sort', '权重')->required(),
            self::input('note', '备注'),

        ];
    }
}