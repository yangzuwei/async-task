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

方式一：在 `task` 文件夹中继承 `AbstractTask` 抽象任务。按照例子中去实现
```
    $lamb = new Lamb('Mary');
    (new TaskClient\SwooleSender())->sendTask($lamb);
```

方式二：执行某个外部命令
执行的外部命令必须都要到config/commands.php中进行注册
（以免有些未经过审核的shell运行，危及系统安全，例如`rm -rf /`），
格式为一维字符串数组，数组元素内容即为需要执行的命令。举例如下：

1. config/commands.php中注册
```php
<?php
return [
    'echo "hello world"',
];
```

2. 使用客户端发送

```php
<?php
    $command = 'php /path/artisan send message';
   （new TaskClient\SwooleSender())->sendCommand($command);

```

## 在CI框架中使用的方法
在CI框架中使用composer支持自动加载；
composer require task/sendmsg,安装当前项目到vendor目录。

在CI框架`config`目录中添加`swoole.php`注册文件，可直接拷贝当前框架中样式，
CI中只用到了客户端，所以我们只需要放IP和端口字段就可以了.

在application目录下（或者别的目录下也行)建立一个任务文件夹，用来存放异步任务，
这些任务类都要继承自`AbstractTask`抽象类。
在本框架中的composer.json文件中的`autoload`字段的`"classmap"`中加入CI框架中刚新建的task任务目录绝对路径，
例如:
```json
  "classmap":["/Users/yangzuwei/Desktop/php/api/application/task"],
```
然后执行在本框架目录下执行` composer dummp-autoload`，然后重启server执行`./bin.reload`。
:dog: