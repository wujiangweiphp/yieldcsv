
### 本例程是关于大量数据以csv格式导出时使用`yield`关键字减少内存使用的示例

### 1. 使用前提

> php >= 5.5


### 2. 使用方法 

> step1 实例化YieldCsv
```php
$yieldCsv = new YieldCsv();
```

> step2 查询数据

#### 2.1 根据文件导入数据

```php
$query_info = array(
       'file_name'=>'your file path',
       'fields'=> array(
               'id' => '编号',
               'nickname' => '昵称',
               'age' => '年龄',
               'desc' => '简介'
            )
       );
$data = $yieldCsv->getFileData($query_info,1);
foreach($data as $k => $v){
    // your oper to insert to mysql
}
```

#### 2.2  根据数据库导出csv文件

```php
$query_info = array(
       'connect_config' => array(
           'host' => 127.0.0.1,
           'dbname' => 'test',
           'username' => 'www',
           'password' => '123456'
       ),
       'query' => 'select id,nickname,age,desc from user ',
       'fields'=> array(
               'id' => '编号',
               'nickname' => '昵称',
               'age' => '年龄',
               'desc' => '简介'
            )
       );
$yieldCsv->exportCsv($query_info,2);
```

#### 2.2  根据数据库查询

```php
$query_info = array(
       'connect_config' => array(
           'host' => 127.0.0.1,
           'dbname' => 'test',
           'username' => 'www',
           'password' => '123456'
       ),
       'query' => 'select id,nickname,age,desc from user ',
       'fields'=> array(
               'id' => '编号',
               'nickname' => '昵称',
               'age' => '年龄',
               'desc' => '简介'
            )
       );
$data = $yieldCsv->getFileData($query_info,2);
foreach($data as $k => $v){
    // your oper code
}
```