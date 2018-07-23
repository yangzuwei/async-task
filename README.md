# async-task
simple task tools by swoole
按照swoole官网中的例子改写的
客户端使用 swoole client 编写


# 用法


在其他地方使用的时候只需要类似如下方式

方式一：在 `task` 文件夹中实现 `TaskInterface` 接口。按照例子中去实现接口
```
    $task = new Lamb();
   （new SwooleClient\TaskClient())->sendTask($task);
```

方式二：执行某个外部命令

```
    $command = 'php /path/artisan send message';
   （new SwooleClient\TaskClient())->sendCommand($command);
```