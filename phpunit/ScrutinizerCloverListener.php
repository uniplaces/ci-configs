<?php

namespace Phpunit;

use Symfony\Component\Process\Process;
use Uniplaces\Library\Stringy\S;

/**
 * Class ScrutinizerCloverListener
 * @package Phpunit
 */
class ScrutinizerCloverListener extends \PHPUnit_Framework_BaseTestListener
{

    const OCULAR_PHAR = 'ocular.phar';
    const OCULAR_HOST = 'https://scrutinizer-ci.com';
    const INSTALL_DIR = '/tmp';
    const SCRUTINIZER_API_TOKEN = '7c8ad0ef7f0a42323cee76eaaf9da7a7339acd24b23ccd09a1dbe2f1c5fbcaa9';

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     * @return bool
     * @throws \Exception
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $file = S::render('{installDir}/{ocularPhar}',
            [
                'installDir' => self::INSTALL_DIR,
                'ocularPhar' => self::OCULAR_PHAR
            ]);

        if (!is_file($file)) {
            if (!$this->downloadOcular()) {
                throw new \Exception('Ocular could not be downloaded');
            }
        }

        if (!$this->uploadCloverReport()) {
            throw new \Exception('Clover report could not be uploaded');
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function uploadCloverReport()
    {
        $process = S::render(
            'php {installDir}/{ocularPhar} code-coverage:upload --access-token="{scrutinizerApiToken}" --format=php-clover {clover}',
            [
                'ocularPhar' => self::OCULAR_PHAR,
                'installDir' => self::INSTALL_DIR,
                'scrutinizerApiToken' => self::SCRUTINIZER_API_TOKEN,
                'clover' => __DIR__ . '/../../docs/phpunit/clover.xml'
            ]);

        return $this->process($process);
    }

    /**
     * @return bool
     */
    protected function downloadOcular()
    {
        $process = S::render(
            'wget {ocularHost}/{ocularPhar} && mv {ocularPhar} {installDir}/{ocularPhar}',
            [
                'ocularHost' => self::OCULAR_HOST,
                'ocularPhar' => self::OCULAR_PHAR,
                'installDir' => self::INSTALL_DIR
            ]);

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
    protected function getProcess($process)
    {
        return new Process($process);
    }
}