<?php

namespace Tests\Feature;


use Illuminate\Support\Facades\Redis;
use PhpParser\Node\Expr\FuncCall;
use Predis\Command\Argument\Geospatial\ByRadius;
use Predis\Command\Argument\Geospatial\FromLonLat;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEqualsCanonicalizing;
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

    public function testSet()
    {
        Redis::del('names');
        Redis::sadd('names', 'Hanif');
        Redis::sadd('names', 'Hanif');
        Redis::sadd('names', 'Ganteng');
        Redis::sadd('names', 'Ganteng');
        Redis::sadd('names', 'Roihan');
        Redis::sadd('names', 'Roihan');

        $response = Redis::smembers('names');
        assertEqualsCanonicalizing(['Hanif', 'Ganteng', 'Roihan'], $response);//cuma data unique
    }

    public function testSortedSet()
    {
        Redis::del('names');
        Redis::zadd('names', 100 , 'Roihan');
        Redis::zadd('names', 85 , 'Ganteng');
        Redis::zadd('names', 95 , 'Hanif');

       $response = Redis::zrange('names', 0, -1);
        assertEquals(['Ganteng', 'Hanif', 'Roihan'], $response); //defaultnya asc
    }

    public function testHash()
    {
        Redis::del('user:1');
        Redis::hset('user:1', 'name', 'Hanif Roihan');
        Redis::hset('user:1', 'email', 'hanif@gans');
        Redis::hset('user:1', 'age', 23);

        $response = Redis::hgetall('user:1');
        assertEquals([
            'name'=>'Hanif Roihan',
            'email'=>'hanif@gans',
            'age'=>'23'//jadi string
        ], $response);
    }

    public function testGeoPoint()
    {
        Redis::del('sellers');
        Redis::geoadd('sellers', 109.4942093, -7.6085098, 'Alfamart Selokerto');
        Redis::geoadd('sellers', 109.4914233, -7.5901979, 'Alfamart Sempor');

        $result = Redis::geodist('sellers', 'Alfamart Selokerto', 'Alfamart Sempor', 'km'); //jarak
        assertEquals(2.0597, $result);

        $result = Redis::georadius('sellers', 109.495918, -7.594867, 5, 'km');//radius 5km , masih mencakup alfa selokerto dan sempor
        assertEquals(['Alfamart Selokerto', 'Alfamart Sempor'], $result);
    }

    public function testHyperLogLog()
    {
        Redis::pfadd('visitors', 'Hanif', 'Ganteng', 'Roihan');
        Redis::pfadd('visitors', 'Hanif', 'Budi', 'Joko');
        Redis::pfadd('visitors', 'Kicaw', 'Budi', 'Joko');

        $result = Redis::pfcount('visitors');
        assertEquals(6, $result); //data unique ada 6
    }

    public function testPipeline()
    {
        Redis::pipeline(function($pipeline){
            $pipeline->setEx('name', 2 ,'Hanif');
            $pipeline->setEx('address', 2 ,'Indonesia');
        });

        $result = Redis::get('name');
        assertEquals('Hanif', $result);

        $result = Redis::get('address');
        assertEquals('Indonesia', $result);
    }

    public function testTransaction()
    {
        Redis::transaction(function($transaction){
            $transaction->setEx('name', 2 ,'Hanif');
            $transaction->setEx('address', 2 ,'Indonesia');
        });

        $result = Redis::get('name');
        assertEquals('Hanif', $result);

        $result = Redis::get('address');
        assertEquals('Indonesia', $result);
    }
    
}
