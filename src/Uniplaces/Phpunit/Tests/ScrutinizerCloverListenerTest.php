<?php

/**
 * This file is part of Uniplaces ci-configs source.
 *
 * (c) Peter Tilsen <peter@uniplaces.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Uniplaces\Tests\Phpunit;

use PHPUnit_Framework_TestCase;
use Mockery;
use Uniplaces\Phpunit\ScrutinizerCloverListener;

/**
 * Class ScrutinizerCloverListenerTest
 *
 * @author Peter Tilsen <peter@uniplaces.com>
 */
class ScrutinizerCloverListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @throws \RuntimeException
     */
    public function testEndTestSuiteDownloadUpload()
    {
        $processMock = Mockery::mock('Symfony\Component\Process\Process')->makePartial();
        $processMock->shouldReceive('run')->andReturn(true);
        $processMock->shouldReceive('isSuccessful')->andReturn(true);

        $listenerMock = Mockery::mock('Uniplaces\Phpunit\ScrutinizerCloverListener')->makePartial();
        $listenerMock->shouldReceive('getProcess')->andReturn($processMock);

        /** @var $listenerMock ScrutinizerCloverListener */
        $this->assertTrue($listenerMock->endTestSuite(new \PHPUnit_Framework_TestSuite()));
    }

    /**
     * @throws \RuntimeException
     * @expectedException \RuntimeException
     */
    public function testEndTestSuiteDownloadUploadFail()
    {
        $processMock = Mockery::mock('Symfony\Component\Process\Process')->makePartial();
        $processMock->shouldReceive('run')->andReturn(true);
        $processMock->shouldReceive('isSuccessful')->andReturn(false);

        $listenerMock = Mockery::mock('Uniplaces\Phpunit\ScrutinizerCloverListener')->makePartial();
        $listenerMock->shouldReceive('getProcess')->andReturn($processMock);

        /** @var $listenerMock ScrutinizerCloverListener */
        $listenerMock->endTestSuite(new \PHPUnit_Framework_TestSuite());
    }
}
