<h2>供应商协同系统 接口</h2>
<h5>开发：子午圈</h5>
<h5>email：1131191695@qq.com</h5>

```php
创建 Migration

php think catch-migrate:create permissions RoleHasPermissions

执行 migrate

php think catch-migrate:run moduleName


`orm操作`

删除某条件的所有记录
$this->xxx->destroy(['id' => 1]);
删除模型下的数据
$xxx->delete()
```