<?php

/**
 * This file is part of Uniplaces ci-configs source.
 *
 * (c) Peter Tilsen <peter@uniplaces.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */


namespace Uniplaces\Phpunit;

use Symfony\Component\Process\Process;

/**
 * Class ScrutinizerCloverListener
 *
 * @author Peter Tilsen <peter@uniplaces.com>
 */
class ScrutinizerCloverListener extends \PHPUnit_Framework_BaseTestListener
{

    const OCULAR_PHAR = 'ocular.phar';
    const OCULAR_HOST = 'https://scrutinizer-ci.com';
    const INSTALL_DIR = '/tmp';
    const API_TOKEN = 'my api token';
    const COVERAGE_CLOVER = 'clover.xml';

    /**
     * @var string
     */
    protected $coverageClover = null;

    /**
     * @var string
     */
    protected $apiToken = null;

    /**
     * @param string $coverageClover
     */
    public function __construct($coverageClover = null, $apiToken = null)
    {
        $this->coverageClover = ($coverageClover !== null) ? $coverageClover : self::COVERAGE_CLOVER;
        $this->apiToken = ($apiToken !== null) ? $apiToken : self::API_TOKEN;
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     *
     * @return bool
     * @throws \Exception
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $file = self::INSTALL_DIR . DIRECTORY_SEPARATOR  . self::OCULAR_PHAR;

        if (!is_file($file) && !$this->downloadOcular()) {
                throw new \RuntimeException('Ocular could not be downloaded');
        }

        if (!$this->uploadCloverReport($this->coverageClover)) {
            throw new \RuntimeException('Clover report could not be uploaded');
        }

        return true;
    }

    /**
     * @param $coverageClover
     *
     * @return bool
     */
    protected function uploadCloverReport($coverageClover)
    {
        $process ='php ' . self::INSTALL_DIR . DIRECTORY_SEPARATOR . self::OCULAR_PHAR
            . ' code-coverage:upload --access-token="' . $this->apiToken . '" --format=php-clover '
            . __DIR__ . DIRECTORY_SEPARATOR . $coverageClover;

        return $this->process($process);
    }

    /**
     * @return bool
     */
    protected function downloadOcular()
    {
        $process = 'pushd ' . self::INSTALL_DIR . ' && wget ' . self::OCULAR_HOST . DIRECTORY_SEPARATOR
            . self::OCULAR_PHAR . ' && popd > /dev/null';
        return $this->process($process);
    }

    /**
     * @param $process
     * @return bool
     */
    protected function process($process)
    {
        $process = $this->getProcess($process);
        $process->run();

        if ($process->isSuccessful()) {
            return true;
        }
        return false;
    }

    /**
     * @param $process
     * @return Process
     */
    public function getProcess($process)
    {
        return new Process($process);
    }
}