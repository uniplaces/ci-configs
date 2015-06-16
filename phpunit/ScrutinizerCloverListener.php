<?php

namespace phpunit;

use Symfony\Component\Process\Process;

/**
 * Class ScrutinizerTestListener
 * @package phpunit
 */
class ScrutinizerTestListener extends \PHPUnit_Framework_BaseTestListener
{

    const OCULAR_PHAR = 'ocular.phar';
    const OCULAR_HOST = 'https://scrutinizer-ci.com';
    const INSTALL_DIR = 'bin';
    const SCRUTINIZER_API_TOKEN = '7c8ad0ef7f0a42323cee76eaaf9da7a7339acd24b23ccd09a1dbe2f1c5fbcaa9';

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     * @return bool
     * @throws \HttpException
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if (!is_file(self::INSTALL_DIR . '/' . self::OCULAR_PHAR )) {
            if (!$this->downloadOcular()) {
                throw new \HttpException('Ocular could not be downloaded');
            }
        }

        if (!$this->uploadCloverReport()) {
            throw new \HttpException('Clover report could not be uploaded');
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function uploadCloverReport()
    {
        return $this->process('php ocular.phar code-coverage:upload --access-token="' . self::SCRUTINIZER_API_TOKEN . '" --format=php-clover docs/phpunit/clover.xml');
    }

    /**
     * @return bool
     */
    protected function downloadOcular()
    {
        return $this->process('wget ' . self::OCULAR_HOST . '/' . self::OCULAR_PHAR . ' ' . self::INSTALL_DIR . '/' . self::OCULAR_PHAR);
    }

    /**
     * @param $process
     * @return bool
     */
    protected function process($process)
    {
        $process = new Process($process);
        $process->run();

        if ($process->isSuccessful()) {
            return true;
        }
        return false;
    }
}