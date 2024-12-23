<?php
use ComBank\OverdraftStrategy\NoOverdraft;
use PHPUnit\Framework\TestCase;

class NoOverdraftTest extends TestCase
{

    /**
     * @test;
     * @dataProvider newAmountsProvider
     * */
    public function testOverdraft($newAmount,$expected){

        //No overdraft grant 0.00 overdraft funds.
        $overdraft = new NoOverdraft();
        $this->assertEquals($expected,$overdraft->isGrantOverdraftFunds($newAmount));
    }

    /**
     * @return array;
     * */
    public function newAmountsProvider()
    {
        return [
            [-1,false],
            [-50,false],
            [-100,false],
            [-101,false]
        ];
    }
}