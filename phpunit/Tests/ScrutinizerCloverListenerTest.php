<?php

namespace phpunit;

use Mockery;

/**
 * Class ScrutinizerCloverListenerTest
 * @package phpunit
 */
class ScrutinizerCloverListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @throws \HttpException
     */
    public function testEndTestSuiteDownloadUpload()
    {
        $processMock = Mockery::mock('Symfony\Component\Process\Process')->makePartial();
        $processMock->shouldReceive('run')->andReturn(true);
        $processMock->shouldReceive('isSuccessful')->andReturn(true);

        $listenerMock = Mockery::mock('ScrutinizerCloverListener')->makePartial();
        $listenerMock->shouldReceive('getProcess')->andReturn($processMock);

        /** @var $listenerMock ScrutinizerCloverListener */
        $this->assertTrue($listenerMock->endTestSuite(new \PHPUnit_Framework_TestSuite()));
    }

    /**
     * @throws \HttpException
     * @expectedException \HttpException
     */
    public function testEndTestSuiteDownloadUploadFail()
    {
        $processMock = Mockery::mock('Symfony\Component\Process\Process')->makePartial();
        $processMock->shouldReceive('run')->andReturn(true);
        $processMock->shouldReceive('isSuccessful')->andReturn(false);

        $listenerMock = Mockery::mock('ScrutinizerCloverListener')->makePartial();
        $listenerMock->shouldReceive('getProcess')->andReturn($processMock);

        /** @var $listenerMock ScrutinizerCloverListener */
        $listenerMock->endTestSuite(new \PHPUnit_Framework_TestSuite());
    }
}
