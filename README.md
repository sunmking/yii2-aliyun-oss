### Yii2 阿里云 OSS

安装

```
composer require mrk/yii2-aliyun-oss:@dev
```

or add

```
"mrk/yii2-aliyun-oss":"@dev"
```


使用

> 在 main.php 文件中做如下修改

```php
components => [
    'oss' => [
        'class' => 'mrk\aliyun\OSS',
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
