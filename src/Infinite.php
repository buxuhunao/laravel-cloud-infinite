<?php

namespace Buxuhunao\CloudInfinite;

use Illuminate\Support\Facades\Facade;
use Overtrue\CosClient\Http\Response;

/**
 * @method static Response subscribe(int $capacity = 1000000, int $qps = 10)
 * @method static Response addImage(string $key, array $param)
 * @method static Response searchImage(string $key, array $query = [])
 * @method static Response deleteImage(string $key, string $EntityId = null)
 * @method static Response ocr(string $key, array $query = [])
 */
class Infinite extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return InfiniteClient::class;
    }
}
