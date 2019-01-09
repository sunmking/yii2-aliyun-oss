### Yii2 阿里云 OSS

安装

```
composer require saviorlv/yii2-aliyun-oss -vvv
```

or add

```
"saviorlv/yii2-aliyun-oss":"^1.0"
```


使用

> 在 main.php 文件中做如下修改

```php
components => [
    'oss' => [
        'class' => 'Saviorlv\Aliyun\OSS',
        'accessKeyId' => 'xxxxx', // 阿里云AccessKeyID
        'accessKeySecret' => 'xxxx', // 阿里云 AccessKeySecret
        'bucket' => 'xxx', // bucket
        'endpoint' => 'http://oss-cn-hangzhou.aliyuncs.com', //OSS节点地址
    ],
]
```

> 在 controller 中

```php
 \Yii::$app->oss->upload($object,$file);
```
