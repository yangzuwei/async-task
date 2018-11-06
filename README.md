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

```
    $command = 'php /path/artisan send message';
   （new TaskClient\SwooleSender())->sendCommand($command);
```

:dog: