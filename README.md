# async-task
simple task tools by swoole
按照swoole官网中的例子改写的
客户端有两种实现方式：
1. 使用 swoole client 实现
2. 使用 stream_socket_client 实现

# 原理
通过<font color="#dd0000">Swoole 的TCP Server </font> 接收客户端发过来的消息，消息都是经过序列化的，用 PHP_EOL 拼接，在 Server 工作进程中再进行拆分。
按照消息类型（目前只支持`AbstractTask`类和linux系统shell类命令）分别运行。
DB采用Mysql PDO 实现，配置了持久化。
Server中的DB资源都是在运行时进行注入的，运行时可以复用一个DB连接，减少了资源消耗。

# 用法

## 启动server

分别执行`bin`目录下对应的shell脚本即可，
- 启动 `./bin/start.sh`
- 关闭 `./bin/stop.sh`
- 重启 `./bin/restart.sh`
- 热更新 `./bin/reload.sh`

## 配置文件
都放在`config`文件夹中，客户端和服务端可以分别部署在不同机器上，只需将`swoole.php`文件中的`ip`字段按照网络情况相应改写即可。

## 添加任务
在其他地方使用的时候只需要类似如下方式

方式一：执行某个外部命令(推荐方式)
建议在各种MVC框架下使用**命令行**形式去执行有关任务，可以避免再去重新学习适应当前这个框架中的逻辑。
执行的外部命令需要进行审核，目前未进行权限和用户的相关处理
（以免有些未经过审核的shell运行，危及系统安全，例如`rm -rf /`），

使用方式
```php
<?php
$command = 'echo "hello world"';
（new TaskClient\SwooleSender())->sendCommand($command);

```

方式二：在 `task` 文件夹中继承 `AbstractTask` 抽象任务。按照例子中去实现
```
    $lamb = new Lamb('Mary');
    (new TaskClient\SwooleSender())->sendTask($lamb);
```
不同项目中，如果使用这种方式，需要注意在server项目中注册client的加载路径，否则会出现命名空间找不到的问题。如下介绍了不同框架中使用该方式的方法：

## 在CI框架中使用的方法
可以按照上述方式二写命令行形式执行。

在CI框架中使用composer支持自动加载；
`composer require wilson_yang/sendtask`,安装当前项目到vendor目录。

在CI框架`config`目录中添加`swoole.php`注册文件，可直接拷贝当前框架中样式，
CI中只用到了客户端，所以我们只需要放IP和端口字段就可以了.

在application目录下（或者别的目录下也行)建立一个任务文件夹，用来存放异步任务，
这些任务类都要继承自`AbstractTask`抽象类。
在本框架中的composer.json文件中的`autoload`字段的`"classmap"`中加入
*CI框架*中刚新建的`task`任务目录绝对路径，
例如:
```json
  "classmap":["/Users/yangzuwei/Desktop/php/api/application/task"],
```
然后执行在**本框架**根目录下执行` composer dump-autoload`，然后重启server执行`./bin.reload`。
:sun: :dog:

## 在laravel框架下使用的方法
`composer require wilson_yang/sendtask`,安装当前项目到vendor目录。
接下来发布配置文件到项目中：
php artisan vendor:publish --provider="Wilson\Async\Provider\TaskServiceProvider"
可以按照上述方式二写命令行形式发送类似`php /path/xxx/artisan command`到server中执行。

如果自己编写了在当前 Laravel 项目中启动server程序（
例如`php artisan task:server start`，
暂时未实现样例，可以自己参考`Laravel`文档实现，
思路:可以借助 exec 类函数去调用 本项目中的`bin`文件夹下的 shell 脚本，
或者使用composer.json 中加入 classmap 字段加载task任务文件夹也可使用（类似CI）。

## 在fpm中执行一些exec类的需要高权限的操作

因为一般的fpm安全限制，我们一般无法使用  exec\shell_exec 之类的 PHP 内建函数。
如果我们使用异步客户端将命令发给以高权限运行的server去执行，那么就可以成功了。
例如我们希望通过网页上的某个按钮去发起服务器上的某shell脚本执行部署操作。

## TODO

考虑异步任务失败情况，回收存储失败任务
