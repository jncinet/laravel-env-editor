## Laravel 编辑 .env文件

* 安装扩展：
```shell
$ composer require jncinet/laravel-env-editor
```

* 使用方法：
```php
// 读取.env键值
app('env-editor')->getEnv($key = null)

// 更新保存.env文件
app('env-editor')->setEnv(['key' => 'value']);
```

* 代码参考自：
 * https://github.com/laravel-admin-extensions/env-manager/blob/master/src/Env.php