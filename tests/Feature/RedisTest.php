<?php

namespace Tests\Feature;


use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class RedisTest extends TestCase
{
    public function testPing()
    {
       $response = Redis::command('ping');
       assertEquals('PONG', $response);

       $response = Redis::ping();// bisa menggunakan magic method, lebih simple. yang penting hapal nama nama perintah di redis
       assertEquals('PONG', $response);
    }
}
