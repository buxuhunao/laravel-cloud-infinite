<?php

namespace Buxuhunao\CloudInfinite;

use Overtrue\CosClient\Client;
use Overtrue\CosClient\Config;
use Overtrue\CosClient\Exceptions\InvalidConfigException;
use Overtrue\CosClient\Support\XML;

class InfiniteClient extends Client
{
    protected const BASE_URI = '/%s?ci-process=ImageSearch&action=%s';

    protected string $baseUri = '';

    public function __construct($config)
    {
        if (! ($config instanceof Config)) {
            $config = new Config($config);
        }

        if (! $config->has('bucket')) {
            throw new InvalidConfigException('No bucket configured.');
        }

        $this->baseUri = \sprintf(
            'https://%s-%s.cos.%s.myqcloud.com/',
            $config->get('bucket'),
            $config->get('app_id'),
            $config->get('region', self::DEFAULT_REGION)
        );

        parent::__construct($config->extend([
            'guzzle' => [
                'base_uri' => $this->baseUri,
            ],
        ]));
    }

    // 开通服务
    public function subscribe(int $capacity = 1000000, int $qps = 10)
    {
        $this->setCiDomain();
        $body = ['Request' => [
            'MaxCapacity' => $capacity,
            'MaxQps' => $qps
        ]];

        return $this->post('ImageSearchBucket', ['body' => XML::fromArray($body)]);
    }

    protected function setCiDomain()
    {
        $baseUri = str_replace('.cos.', '.ci.', $this->baseUri);

        $this->setHttpClientOptions(['base_uri' => $baseUri]);
    }

    // 添加图片
    public function addImage(string $key, array $param)
    {
        if (! array_key_exists('EntityId', $param)) {
            $param['EntityId'] = $key;
        }

        return $this->post(
            sprintf(static::BASE_URI, $key, 'AddImage'),
            ['body' => XML::fromArray(['Request' => $param])]
        );
    }

    // 搜索图片
    public function searchImage(string $key, array $query = [])
    {
        $uri = sprintf(self::BASE_URI, $key, 'SearchImage');
        $uri = trim($uri . '&' . http_build_query($query), '&');

        return $this->get($uri);
    }

    // 删除图片
    public function deleteImage(string $key, string $EntityId)
    {
        return $this->post(
            sprintf(self::BASE_URI, $key, 'DeleteImage'),
            ['body' => XML::fromArray(['Request' => compact('EntityId')])]
        );
    }

    public function ocr(string $key, array $query = [])
    {
        return $this->get(
            sprintf(self::BASE_URI, $key, compact('query'))
        );
    }
}
