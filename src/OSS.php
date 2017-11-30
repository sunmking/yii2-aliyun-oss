<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2016/3/16 18:56
 * description:
 */

namespace mrk\aliyun;

use Yii;
use OSS\OssClient;
use yii\base\Component;
use yii\base\InvalidConfigException;

class OSS extends Component
{
    /**
     * @var string OSS AccessKeyID
     */
    public $accessKeyId;

    /**
     * @var string OSS AccessKeySecret
     */
    public $accessKeySecret;

    /**
     * @var string OSS bucket
     */
    public $bucket;

    /**
     * @var string OSS内网地址, 如:oss-cn-hangzhou-internal.aliyuncs.com
     */
    public $lanDomain;

    /**
     * @var string OSS外网地址, 如:oss-cn-hangzhou.aliyuncs.com
     */
    public $wanDomain;

    /**
     * @var OssClient
     */
    private $_ossClient;

    /**
     * 从lanDomain和wanDomain中选取, 默认走外网
     * @var string 最终操作域名
     */
    protected $baseUrl;

    /**
     * @var bool 是否私有空间, 默认公开空间
     */
    public $isPrivate = false;

    /**
     * @var bool 上传文件是否使用内网，免流量费
     */
    public $isInternal = false;

    public function init()
    {
        if ($this->accessKeyId === null) {
            throw new InvalidConfigException('The "accessKeyId" property must be set.');
        } elseif ($this->accessKeySecret === null) {
            throw new InvalidConfigException('The "accessKeySecret" property must be set.');
        } elseif ($this->bucket === null) {
            throw new InvalidConfigException('The "bucket" property must be set.');
        } elseif ($this->lanDomain === null) {
            throw new InvalidConfigException('The "lanDomain" property must be set.');
        } elseif ($this->wanDomain === null) {
            throw new InvalidConfigException('The "wanDomain" property must be set.');
        }

        $this->baseUrl = $this->isInternal ? $this->lanDomain : $this->wanDomain;
    }

    /**
     * @return \OSS\OssClient
     */
    public function getClient()
    {
        if ($this->_ossClient === null) {
            $this->setClient(new OssClient($this->accessKeyId, $this->accessKeySecret, $this->baseUrl));
        }
        return $this->_ossClient;
    }

    /**
     * @param \OSS\OssClient $ossClient
     */
    public function setClient(OssClient $ossClient)
    {
        $this->_ossClient = $ossClient;
    }

    /**
     * @param $path
     * @return bool
     */
    public function has($path)
    {
        return $this->getClient()->doesObjectExist($this->bucket, $path);
    }

    /**
     * @param $path
     * @return array|bool
     * @throws \OSS\Core\OssException
     */
    public function read($path)
    {
        if (!($resource = $this->readStream($path))) {
            return false;
        }
        $resource['contents'] = stream_get_contents($resource['stream']);
        fclose($resource['stream']);
        unset($resource['stream']);
        return $resource;
    }

    /**
     * @param $path
     * @return array|bool
     * @throws \OSS\Core\OssException
     */
    public function readStream($path)
    {
        $url = $this->getClient()->signUrl($this->bucket, $path, 3600);
        $stream = fopen($url, 'r');
        if (!$stream) {
            return false;
        }
        return compact('stream', 'path');
    }

    /**
     * @param $object
     * @param $filePath ## 要上传文件的绝对路径
     * @return null
     * @throws \OSS\Core\OssException
     */
    public function upload($object, $filePath)
    {
        return $this->getClient()->uploadFile($this->bucket, $object, $filePath);
    }

    /**
     * 删除单个文件
     * @param $object
     * @return bool
     */
    public function delete($object)
    {
        return $this->getClient()->deleteObject($this->bucket, $object) === null;
    }

    /**
     * 批量删除
     * @param array $object
     * @return bool
     * @throws null
     */
    public function batchDelete(array $object)
    {
        return $this->getClient()->deleteObjects($this->bucket, $object) === null;
    }

    /**
     * 创建文件夹
     * @param $dirName
     * @return array|bool
     */
    public function createDir($dirName)
    {
        $result = $this->getClient()->createObjectDir($this->bucket, rtrim($dirName, '/'));
        if ($result !== null) {
            return false;
        }
        return ['path' => $dirName];
    }

    /**
     * @param array $options
     * @return array
     * @throws \OSS\Core\OssException
     */
    public function getAllObject($options = [])
    {
        $objectListing = $this->getClient()->listObjects($this->bucket, $options);
        $objectKeys = [];
        foreach ($objectListing->getObjectList() as $objectSummary) {
            $objectKeys[] = $objectSummary->getKey();
        }
        return $objectKeys;
    }

    /**
     * @param null $options
     * @return \OSS\Model\BucketListInfo
     * @throws \OSS\Core\OssException
     */
    public function getAllBucket($options = null){
        return $this->getClient()->listBuckets($options);
    }
}
