<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Users extends Migrator
{
    public function change()
    {
        $table = $this->table('users', array('engine' => 'Innodb', 'comment' => '用户表', 'signed' => false));
        $table->addColumn('username', 'string', array('limit' => 15, 'default' => '', 'comment' => '用户名'))
            ->addColumn('password', 'string', array('limit' => 255, 'comment' => '用户密码'))
            ->addColumn('email', 'string', array('limit' => 100, 'comment' => '邮箱 登录'))
            ->addColumn('creator_id', 'integer', ['default' => 0, 'comment' => '创建人ID'])
            ->addColumn('department_id', 'integer', ['default' => 0, 'comment' => '部门ID'])
            ->addColumn('customer_id', 'integer', ['default' => 0, 'comment' => '客户ID'])
            ->addColumn('company_id', 'integer', ['default' => 0, 'comment' => '公司ID'])
            ->addColumn('status', 'boolean', array('limit' => 1, 'default' => 1, 'comment' => '用户状态 1 正常 2 禁用'))
            ->addColumn('last_login_ip', 'string', array('limit' => 50, 'default' => 0, 'comment' => '最后登录IP'))
            ->addColumn('last_login_time', 'integer', array('default' => 0, 'comment' => '最后登录时间', 'signed' => false))
            ->addColumn('created_at', 'integer', array('default' => 0, 'comment' => '创建时间', 'signed' => false))
            ->addColumn('updated_at', 'integer', array('default' => 0, 'comment' => '更新时间', 'signed' => false))
            ->addColumn('deleted_at', 'integer', array('default' => 0, 'comment' => '删除状态，0未删除 >0 已删除', 'signed' => false))
            ->create();
    }
}
