<?php

namespace App\Tests;

use App\Document\Token;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CacheTest extends KernelTestCase
{
    private $cache;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->cache = self::$container->get('Symfony\Contracts\Cache\CacheInterface');
        $this->cache->delete('ronaldo');
        $this->cache->delete('ronaldoSkills');
    }

    public function testCacheWorks()
    {
        $ronaldo = $this->cache->get('ronaldo', function(){
            return 'rei da quadra';
        });

        $this->assertTrue('rei da quadra' === $ronaldo);
        $this->assertNotEquals('pipoqueiro', $ronaldo);

        $skills = $this->cache->get('ronaldoSkills', function(){
            return [
                'typescript'    => 'mais de 8000',
                'php'           => 'mais de 8000',
                'laravel'       => 'mais de 8000',
                'symfony'       => '7999',
            ];
        });

        $this->assertEquals('7999', $skills['symfony']);
        
        $this->cache->delete('ronaldoSkills');
        $skills = $this->cache->get('ronaldoSkills', function(){
            return '';
        });
        $this->assertEmpty($skills);
        

    }
}