<?php

namespace App\Services\Qcloud;

use Illuminate\Support\Facades\Log;
use Qcloud\Cos\Client;

class Cos
{
    //替换为用户的 secretId，请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi
    protected string $secretId  = "";

    //替换为用户的 secretKey，请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi
    protected string $secretKey = "";

    //替换为用户的 region，已创建桶归属的region可以在控制台查看，https://console.cloud.tencent.com/cos5/bucket
    protected string $region    = "";

    //替换为用户的 region，已创建桶归属的region可以在控制台查看，https://console.cloud.tencent.com/cos5/bucket
    protected string $bucket    = "";

    protected Client $cosClient;

    public function __construct()
    {
        $this->secretId = config('app.qcloud_secret_Id');
        $this->secretKey= config('app.qcloud_secret_key');
        $this->region   = config('app.qcloud_region');
        $this->bucket   = config('app.qcloud_bucket');

        $this->cosClient = new Client(
            array(
                'region' => $this->region,
                'scheme' => 'https', //协议头部，默认为http
                'credentials' => array(
                    'secretId' => $this->secretId,
                    'secretKey' => $this->secretKey
                )
            )
        );
    }

    /**
     * 上传一个本地文件到 cos
     * @param string $local_path
     * @param $key
     * @return void
     */
    public function upload(string $local_path, $key)
    {
        $result = null;
        try {
            // Log::debug('$this->bucket=>' . $this->bucket);
            $result = $this->cosClient->upload(
                $this->bucket,      //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
                $key,               //此处的 key 为对象键
                fopen($local_path, 'rb')
            );
            // 请求成功
            // Log::info($result);

        } catch (\Exception $e) {
            // 请求失败
            Log::error($e);
        }
        return $result;
    }

    public function getObjectUrl($key): mixed{
        try {
            return $signedUrl = $this->cosClient->getObjectUrl($this->bucket, $key, '+24 hours');
        }
        catch
        (\Exception $e) {
            // 请求失败
            print_r($e);
        }
        return false;
    }

    public function getObjectUrlWithoutSign($key): mixed
    {
        try {
            // 请求成功
            return $this->cosClient->getObjectUrlWithoutSign($this->bucket, $key);
        } catch (\Exception $e) {
            // 请求失败
            print_r($e);
            return false;
        }

    }
}
