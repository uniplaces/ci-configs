<?php

namespace Uniplaces\Phpunit;

use Symfony\Component\Process\Process;

/**
 * Class ScrutinizerCloverListener
 * @package Uniplaces
 */
class ScrutinizerCloverListener extends \PHPUnit_Framework_BaseTestListener
{

    const OCULAR_PHAR = 'ocular.phar';
    const OCULAR_HOST = 'https://scrutinizer-ci.com';
    const INSTALL_DIR = '/tmp';
    const SCRUTINIZER_API_TOKEN = '7c8ad0ef7f0a42323cee76eaaf9da7a7339acd24b23ccd09a1dbe2f1c5fbcaa9';

    /**
     * @var string
     */
    protected $coverageClover = '';

    /**
     * @param string $coverageClover
     */
    public function __construct($coverageClover = null)
    {
        $this->coverageClover = ($coverageClover !== null) ? $coverageClover : 'clover.xml';
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

        if (!is_file($file)) {
            if (!$this->downloadOcular()) {
                throw new \Exception('Ocular could not be downloaded');
            }
        }

        if (!$this->uploadCloverReport($this->coverageClover)) {
            throw new \Exception('Clover report could not be uploaded');
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function uploadCloverReport($coverageClover)
    {
        $process ='php ' . self::INSTALL_DIR . DIRECTORY_SEPARATOR . self::OCULAR_PHAR . ' code-coverage:upload --access-token="'
            . self::SCRUTINIZER_API_TOKEN . '" --format=php-clover ' . __DIR__ . DIRECTORY_SEPARATOR . $coverageClover;

        return $this->process($process);
    }

    /**
     * @return bool
     */
    protected function downloadOcular()
    {
        $process = 'pushd ' . self::INSTALL_DIR . ' && wget ' . self::OCULAR_HOST . DIRECTORY_SEPARATOR . self::OCULAR_PHAR
            . ' && popd > /dev/null';
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