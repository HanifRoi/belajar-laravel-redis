<?php

namespace Tests\Feature;


use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;

class RedisTest extends TestCase
{
    public function testPing()
    {
       $response = Redis::command('ping');
       assertEquals('PONG', $response);

       $response = Redis::ping();// bisa menggunakan magic method, lebih simple. yang penting hapal nama nama perintah di redis
       assertEquals('PONG', $response);
    }

    public function testString(){
        Redis::setEx('name', 2, 'hanif');//nama hanif, expired 2 detik
        $response = Redis::get('name');
        assertEquals('hanif', $response);

        sleep(5);

        $response = Redis::get('name');//karena waktu sleep  5 detik lebih dari 2 detik
        assertNull($response);
        
    }
    
}
