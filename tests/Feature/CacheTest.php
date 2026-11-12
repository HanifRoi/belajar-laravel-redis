<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;

class CacheTest extends TestCase
{
    public function testCache()
    {
        Cache::put('name', 'hanif', 2);
        Cache::put('address', 'indonesia', 2);

        $result = Cache::get('name');
        assertEquals('hanif', $result);

        $result = Cache::get('address');
        assertEquals('indonesia', $result);

        sleep(5);

        $result = Cache::get('name');
        assertNull($result);

        $result = Cache::get('address');
        assertNull($result);
    }
}
