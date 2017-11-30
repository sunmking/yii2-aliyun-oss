<?php
namespace mrk\aliyun;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use OSS\OssClient;
use OSS\Core\OssException;

/**
 * Class OSS
 * @package mrk\aliyun
 * @author Mr King
 */
class OSS extends Component
{
    /**
     * @var
     */
    public $accessKeyId;

    /**
     * @var
     */
    public $accessKeySecret;

    /**
     * @var
     */
    public $bucket;

    /**
     * @var string 节点地址, 如:oss-cn-hangzhou.aliyuncs.com
     */
    public $endpoint;

    /**
     * @var
     */
    private $_client;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->accessKeyId === null) {
            throw new InvalidConfigException('The "accessKeyId" property must be set.');
        }
        if ($this->accessKeySecret === null) {
            throw new InvalidConfigException('The "accessKeySecret" property must be set.');
        }
        if ($this->bucket === null) {
            throw new InvalidConfigException('The "bucket" property must be set.');
        }
        if ($this->endpoint === null) {
            throw new InvalidConfigException('The "endpoint" property must be set.');
        }
        try {
            $this->_client = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        } catch (OssException $e) {
            throw new InvalidConfigException($e->getMessage());
        }
    }

    /**
     * @param $object
     * @param $filePath
     * @return mixed
     */
    public function upload($object, $filePath)
    {
        return $this->_client->uploadFile($this->bucket, $object, $filePath);
    }

    /**
     * 删除单个文件
     * @param $object
     * @return bool
     */
    public function delete($object)
    {
        return $this->_client->deleteObject($this->bucket, $object) === null;
    }

    /**
     * 批量删除
     * @param array $object
     * @return bool
     * @throws null
     */
    public function batchDelete(array $object)
    {
        return $this->_client->deleteObjects($this->bucket, $object) === null;
    }

    /**
     * @param $dirName
     * @return bool
     */
    public function createDir($dirName)
    {
        $result = $this->_client->createObjectDir($this->bucket, rtrim($dirName, '/'));
        if ($result !== null) {
            return false;
        }
        return true;
    }

    /**
     * @param array $options
     * @return array
     */
    public function getAllObject($options = [])
    {
        $objectListing = $this->_client->listObjects($this->bucket, $options);
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
        return $this->_client->listBuckets($options);
    }
}
