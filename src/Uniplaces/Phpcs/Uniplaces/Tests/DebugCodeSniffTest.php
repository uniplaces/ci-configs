<?php
/**
 * Class Uniplaces_Sniffs_Debug_DebugCodeSniffTest
 */
class Uniplaces_Sniffs_Debug_DebugCodeSniffTest extends \PHPUnit_Framework_TestCase
{

    public function testProcessTComment()
    {
        $codeSnifferFile = Mockery::mock('PHP_CodeSniffer_File')->makePartial();
        $codeSnifferFile->shouldReceive('addError')->andReturn(true);
        $codeSnifferFile->shouldReceive('getTokens')->andReturn([['type' => 'T_COMMENT', 'content' => 'print_r(']]);

        $debugCodeSniff = new Uniplaces_Sniffs_Debug_DebugCodeSniff();
        $this->assertTrue($debugCodeSniff->process($codeSnifferFile, 0));
    }

    public function testProcessTStringPrintR()
    {
        $codeSnifferFile = Mockery::mock('PHP_CodeSniffer_File')->makePartial();
        $codeSnifferFile->shouldReceive('addError')->andReturn(true);
        $codeSnifferFile->shouldReceive('getTokens')->andReturn([['type' => 'T_STRING', 'content' => 'print_r']]);

        $debugCodeSniff = new Uniplaces_Sniffs_Debug_DebugCodeSniff();
        $this->assertTrue($debugCodeSniff->process($codeSnifferFile, 0));
    }

    public function testProcessTStringDebug()
    {
        $codeSnifferFile = Mockery::mock('PHP_CodeSniffer_File')->makePartial();
        $codeSnifferFile->shouldReceive('addError')->andReturn(true);
        $codeSnifferFile->shouldReceive('getTokens')->andReturn([['type' => 'T_STRING', 'content' => 'debug']]);

        $debugCodeSniff = new Uniplaces_Sniffs_Debug_DebugCodeSniff();
        $this->assertTrue($debugCodeSniff->process($codeSnifferFile, 0));
    }

    public function testProcessTStringDebugStaticFunction()
    {
        $codeSnifferFile = Mockery::mock('PHP_CodeSniffer_File')->makePartial();
        $codeSnifferFile->shouldReceive('findPrevious')->andReturn(1);
        $codeSnifferFile->shouldReceive('addError')->andReturn(true);
        $codeSnifferFile->shouldReceive('getTokens')->andReturn([['type' => 'T_STRING', 'content' => 'test'], ['type' => 'T_STRING', 'content' => '::'], ['type' => 'T_STRING', 'content' => 'debug']]);

        $debugCodeSniff = new Uniplaces_Sniffs_Debug_DebugCodeSniff();
        $this->assertTrue($debugCodeSniff->process($codeSnifferFile, 2));
    }

    public function testProcessTStringDebugPartOfName()
    {
        $codeSnifferFile = Mockery::mock('PHP_CodeSniffer_File')->makePartial();
        $codeSnifferFile->shouldReceive('findPrevious')->andReturn(0);
        $codeSnifferFile->shouldReceive('addError')->andReturn(true);
        $codeSnifferFile->shouldReceive('getTokens')->andReturn([['type' => 'T_STRING', 'content' => '->'], ['type' => 'T_STRING', 'content' => 'debug']]);

        $debugCodeSniff = new Uniplaces_Sniffs_Debug_DebugCodeSniff();
        $this->assertTrue($debugCodeSniff->process($codeSnifferFile, 1));
    }
}
