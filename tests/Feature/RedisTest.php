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

    public function testList()
    {
        Redis::del('names');
        Redis::rpush('names', 'Hanif');
        Redis::rpush('names', 'Ganteng');
        Redis::rpush('names', 'Roihan');

        $response = Redis::lrange('names', 0, -1);//mengambil nama dari index 0 sampe yang terakhir
        assertEquals(['Hanif', 'Ganteng', 'Roihan'], $response);

        assertEquals('Hanif', Redis::lpop('names'));
        assertEquals('Ganteng', Redis::lpop('names'));
        assertEquals('Roihan', Redis::lpop('names'));
    }
    
}
