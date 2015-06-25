<?php

/**
 * This file is part of Uniplaces ci-configs source.
 *
 * (c) Peter Tilsen <peter@uniplaces.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Class Uniplaces_Sniffs_PHP_ControllerFunctionsSniffTest
 *
 * @author Peter Tilsen <peter@uniplaces.com>
 */
// @codingStandardsIgnoreStart
class Uniplaces_Sniffs_PHP_ControllerFunctionsSniffTest extends PHPUnit_Framework_TestCase
{
// @codingStandardsIgnoreEnd
    public function testRegister()
    {
        $forbiddenFunctionsSniff = new Uniplaces_Sniffs_PHP_ControllerFunctionsSniff();
        $this->assertEquals([T_CLASS, T_PUBLIC, T_STRING], $forbiddenFunctionsSniff->register());
    }

    public function testProcessIsForbidden()
    {
        $codeSnifferFile = Mockery::mock('PHP_CodeSniffer_File')->makePartial();
        $codeSnifferFile->shouldReceive('findPrevious')->andReturn(3);
        $codeSnifferFile->shouldReceive('findNext')->andReturn(5);
        $codeSnifferFile->shouldReceive('addError')->andReturn(true);
        $codeSnifferFile->shouldReceive('getTokens')->andReturn([
            ['type' => 'T_CLASS', 'content' => 'classController', 'code' => T_CLASS],
            ['type' => 'T_PUBLIC', 'content' => 'public', 'code' => T_PUBLIC],
            ['type' => 'T_STRING', 'content' => ' ', 'code' => T_STRING],
            ['type' => 'T_FUNCTION', 'content' => 'function', 'code' => T_FUNCTION],
            ['type' => 'T_STRING', 'content' => 'testFunction', 'code' => T_STRING],
            ['type' => 'T_STRING', 'content' => 'text', 'code' => T_STRING]]);

        $forbiddenFunctionsSniff = new Uniplaces_Sniffs_PHP_ControllerFunctionsSniff();
        $this->assertTrue($forbiddenFunctionsSniff->process($codeSnifferFile, 4));
    }

    public function testProcessIsNotForbidden()
    {
        $codeSnifferFile = Mockery::mock('PHP_CodeSniffer_File')->makePartial();
        $codeSnifferFile->shouldReceive('findPrevious')->andReturn(3);
        $codeSnifferFile->shouldReceive('findNext')->andReturn(5);
        $codeSnifferFile->shouldReceive('addError')->andReturn(true);
        $codeSnifferFile->shouldReceive('getTokens')->andReturn([
            ['type' => 'T_CLASS', 'content' => 'classController', 'code' => T_CLASS],
            ['type' => 'T_PUBLIC', 'content' => 'public', 'code' => T_PUBLIC],
            ['type' => 'T_STRING', 'content' => ' ', 'code' => T_STRING],
            ['type' => 'T_FUNCTION', 'content' => 'function', 'code' => T_FUNCTION],
            ['type' => 'T_STRING', 'content' => 'testFunctionAction', 'code' => T_STRING],
            ['type' => 'T_STRING', 'content' => 'text', 'code' => T_STRING]]);

        $forbiddenFunctionsSniff = new Uniplaces_Sniffs_PHP_ControllerFunctionsSniff();
        $this->assertTrue($forbiddenFunctionsSniff->process($codeSnifferFile, 4));
    }


    public function testProcessIsMagicMethod()
    {
        $codeSnifferFile = Mockery::mock('PHP_CodeSniffer_File')->makePartial();
        $codeSnifferFile->shouldReceive('findPrevious')->andReturn(3);
        $codeSnifferFile->shouldReceive('findNext')->andReturn(5);
        $codeSnifferFile->shouldReceive('addError')->andReturn(true);
        $codeSnifferFile->shouldReceive('getTokens')->andReturn([
            ['type' => 'T_CLASS', 'content' => 'classController', 'code' => T_CLASS],
            ['type' => 'T_PUBLIC', 'content' => 'public', 'code' => T_PUBLIC],
            ['type' => 'T_STRING', 'content' => ' ', 'code' => T_STRING],
            ['type' => 'T_FUNCTION', 'content' => 'function', 'code' => T_FUNCTION],
            ['type' => 'T_STRING', 'content' => '__construct', 'code' => T_STRING],
            ['type' => 'T_STRING', 'content' => 'text', 'code' => T_STRING]]);

        $forbiddenFunctionsSniff = new Uniplaces_Sniffs_PHP_ControllerFunctionsSniff();
        $this->assertTrue($forbiddenFunctionsSniff->process($codeSnifferFile, 4));
    }


    public function testProcessIsController()
    {
        $codeSnifferFile = Mockery::mock('PHP_CodeSniffer_File')->makePartial();
        $codeSnifferFile->shouldReceive('findPrevious')->andReturn(3);
        $codeSnifferFile->shouldReceive('findNext')->andReturn(5);
        $codeSnifferFile->shouldReceive('addError')->andReturn(true);
        $codeSnifferFile->shouldReceive('getTokens')->andReturn([
            ['type' => 'T_STRING', 'content' => 'comment', 'code' => T_STRING],
            ['type' => 'T_STRING', 'content' => 'comment', 'code' => T_STRING],
            ['type' => 'T_STRING', 'content' => 'comment', 'code' => T_STRING],
            ['type' => 'T_STRING', 'content' => 'comment', 'code' => T_STRING],
            ['type' => 'T_CLASS', 'content' => 'class', 'code' => T_CLASS],
            ['type' => 'T_STRING', 'content' => 'classController', 'code' => T_STRING]]);

        $forbiddenFunctionsSniff = new Uniplaces_Sniffs_PHP_ControllerFunctionsSniff();
        $this->assertTrue($forbiddenFunctionsSniff->process($codeSnifferFile, 4));
    }


    public function testProcessIsPrivate()
    {
        $codeSnifferFile = Mockery::mock('PHP_CodeSniffer_File')->makePartial();
        $codeSnifferFile->shouldReceive('findPrevious')->andReturn(3);
        $codeSnifferFile->shouldReceive('findNext')->andReturn(5);
        $codeSnifferFile->shouldReceive('addError')->andReturn(true);
        $codeSnifferFile->shouldReceive('getTokens')->andReturn([
            ['type' => 'T_CLASS', 'content' => 'classController', 'code' => T_CLASS],
            ['type' => 'T_PRIVATE', 'content' => 'private', 'code' => T_PRIVATE],
            ['type' => 'T_STRING', 'content' => ' ', 'code' => T_STRING],
            ['type' => 'T_FUNCTION', 'content' => 'function', 'code' => T_FUNCTION],
            ['type' => 'T_STRING', 'content' => '__construct', 'code' => T_STRING],
            ['type' => 'T_STRING', 'content' => 'text', 'code' => T_STRING]]);

        $forbiddenFunctionsSniff = new Uniplaces_Sniffs_PHP_ControllerFunctionsSniff();
        $this->assertTrue($forbiddenFunctionsSniff->process($codeSnifferFile, 4));
    }
}
