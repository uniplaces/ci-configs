<?php
/**
 * This file is part of Uniplaces ci-configs source.
 *
 * (c) Daniel Gomes <daniel@uniplaces.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Uniplaces_Sniffs_PHP_FunctionCommentSniffUnitTest
 *
 * @author Daniel Gomes <daniel@uniplaces.com>
 */
class Uniplaces_Sniffs_PHP_FunctionCommentSniffUnitTest extends AbstractSniffUnitTest
{
    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile = '')
    {
        switch ($testFile) {
            case 'FunctionCommentUnitTest.1.inc':
                return [
                    17 => 1,
                    24 => 1,
                    31 => 2,
                    38 => 1
                ];
            case 'FunctionCommentUnitTest.2.inc':
                return [
                    20 => 1,
                    30 => 1,
                    40 => 2,
                    50 => 1
                ];
        }

        return [];
    }

    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile = '')
    {
        switch ($testFile) {
            case 'FunctionCommentUnitTest.1.inc':
                return [];
            case 'FunctionCommentUnitTest.2.inc':
                return [
                    58 => 1
                ];
        }

        return [];
    }
}
