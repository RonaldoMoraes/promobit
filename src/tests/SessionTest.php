<?php

namespace App\Tests;

use App\Document\Token;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SessionTest extends KernelTestCase
{
    private $sessionUtil;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->sessionUtil = self::$container->get('App\Util\SessionUtil');
    }

    public function testSessionWorks()
    {
        $this->sessionUtil->set('ronaldo', 'rei da quadra');
        $ronaldo = $this->sessionUtil->get('ronaldo');

        $this->assertTrue($this->sessionUtil->compareValue('ronaldo', 'rei da quadra'));
        $this->assertEquals('rei da quadra', $ronaldo);
        $this->assertNotEquals('pipoqueiro', $ronaldo);
        
        $skills = [
            'typescript'    => 'mais de 8000',
            'php'           => 'mais de 8000',
            'laravel'       => 'mais de 8000',
            'symfony'       => '7999',
        ];
        $this->sessionUtil->set('ronaldoSkills', $skills);

        $this->assertTrue($this->sessionUtil->compareValue('ronaldoSkills', $skills));
        $this->assertEquals('7999', $this->sessionUtil->get('ronaldoSkills')['symfony']);


    }
}