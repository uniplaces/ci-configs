<?php

class Uniplaces_Sniffs_PHP_ForbiddenFunctionsSniffTest extends PHPUnit_Framework_TestCase
{

    public function testRegister()
    {
        $forbiddenFunctionsSniff = new Uniplaces_Sniffs_PHP_ForbiddenFunctionsSniff();
        $this->assertEquals([T_STRING], $forbiddenFunctionsSniff->register());
    }

    public function testProcessPrevTNSSeparator()
    {
        $codeSnifferFile = Mockery::mock('PHP_CodeSniffer_File')->makePartial();
        $codeSnifferFile->shouldReceive('findPrevious')->andReturn(0);
        $codeSnifferFile->shouldReceive('findNext')->andReturn(2);
        $codeSnifferFile->shouldReceive('addError')->andReturn(true);
        $codeSnifferFile->shouldReceive('getTokens')->andReturn([
            ['type' => 'T_STRING', 'content' => '->', 'code' => T_NS_SEPARATOR],
            ['type' => 'T_STRING', 'content' => 'sprintf'], ['type' => 'T_STRING', 'content' => 'test', 'code' => T_NS_SEPARATOR]]);

        $forbiddenFunctionsSniff = new Uniplaces_Sniffs_PHP_ForbiddenFunctionsSniff();
        $this->assertTrue($forbiddenFunctionsSniff->process($codeSnifferFile, 1));
    }
}
