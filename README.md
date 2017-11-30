Yii2 Aliyun OSS
===============
Yii2 阿里云 OSS

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiier/yii2-aliyun-oss "*"
```

or add

```
"yiier/yii2-aliyun-oss": "*"
```

to the require section of your `composer.json` file.


Usage
-----

配置文件添加组件  :

```php
components => [
    'oss' => [
        'class' => 'yiier\AliyunOSS\OSS',
        'accessKeyId' => 'xxxxx', // 阿里云OSS AccessKeyID
        'accessKeySecret' => 'xxxx', // 阿里云OSS AccessKeySecret
        'bucket' => 'xxx', // 阿里云的bucket空间
        'lanDomain' => 'oss-cn-hangzhou-internal.aliyuncs.com', // OSS内网地址
        'wanDomain' => 'oss-cn-hangzhou.aliyuncs.com', //OSS外网地址
        'isInternal' => true // 上传文件是否使用内网，免流量费（选填，默认 false 是外网）
    ],
]
```

```php
/** @var \yiier\AliyunOSS\OSS $oss */
$oss = \Yii::$app->get('oss');
$fh = '/vagrant/php/baseapi/web/storage/image/824edb4e295892aedb8c49e4706606d6.png';
$oss->upload('824edb4e295892aedb8c49e4706606d6.png', $fh);

或者

$oss->upload('storage/image/824edb4e295892aedb8c49e4706606d6.png', $fh); // 会自动创建文件夹

其他用法

$oss->createDir('storage/image/'); //创建文件夹
$oss->delete('824edb4e295892aedb8c49e4706606d6.png'); // 删除文件
$oss->delete('storage/image/824edb4e295892aedb8c49e4706606d6.png'); // 删除文件，如果这个文件是此文件夹的最后一个文件，则会把文件夹一起删除
$oss->delete('storage/image/'); // 删除文件夹，但是要确保是空文件夹
$oss->getAllObject(); // 获取根目录下的所有文件名，默认是100个
$oss->getAllObject(['prefix' => 'storage/image/']); // 获取 `storage/image/` 目录下的所有文件名，默认是100个
```
