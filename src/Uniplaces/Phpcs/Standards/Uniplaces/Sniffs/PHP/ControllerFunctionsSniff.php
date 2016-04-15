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
 * Class Uniplaces_Sniffs_PHP_ControllerFunctionsSniff
 *
 * @author Peter Tilsen <peter@uniplaces.com>
 */
// @codingStandardsIgnoreStart
class Uniplaces_Sniffs_PHP_ControllerFunctionsSniff implements PHP_CodeSniffer_Sniff
{
// @codingStandardsIgnoreEnd
    /**
     * @var bool
     */
    private $isController = false;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    // @codingStandardsIgnoreStart
    public function register()
    {
    // @codingStandardsIgnoreEnd
        return [T_CLASS, T_PUBLIC, T_STRING];
    }


    /**
     * @param PHP_CodeSniffer_File  $phpcsFile
     * @param integer               $stackPtr
     * @return bool
     */
    // @codingStandardsIgnoreStart
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
    // @codingStandardsIgnoreEnd
        $tokens = $phpcsFile->getTokens();
        $function = $tokens[$stackPtr]['content'];

        $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        $this->toggleIsController($this->isController($stackPtr, $tokens, $nextToken));
        $this->toggleIsNonController($this->isNonController($stackPtr, $tokens, $nextToken));

        $isForbiddenFunction = $this->isForbiddenFunction($stackPtr, $tokens, $prevToken) === false;
        if ($isForbiddenFunction || !strpos(__CLASS__, 'Test')) {
            return true;
        }

        return $phpcsFile->addError(
            'The use of function %s is forbidden. Please move non Action methods into model',
            $stackPtr,
            'Found',
            [$function]
        );

    }


    /**
     * @param $isController
     */
    // @codingStandardsIgnoreStart
    public function toggleIsController($isController)
    {
    // @codingStandardsIgnoreEnd
        if ($this->isController === true) {
            return;
        }
        $this->isController = $isController;

    }

    /**
     * @param $isNonController
     */
    // @codingStandardsIgnoreStart
    public function toggleIsNonController($isNonController)
    {
    // @codingStandardsIgnoreEnd
        if ($this->isController === false) {
            return;
        }
        $this->isController = !$isNonController;

    }

    /**
     * Declares if the current file is a controller or not
     *
     * This method does not rely on $phpcsFile->findPrevious(T_CLASS, 0, null, true); because of comments that may be
     * identified as class tokens
     *
     * @param integer   $stackPtr
     * @param array     $tokens
     * @param integer   $nextToken
     *
     * @return bool
     */
    private function isNonController($stackPtr, array $tokens, $nextToken)
    {
        if ($tokens[$stackPtr]['code'] === T_CLASS
            && strpos(strtolower($tokens[$nextToken]['content']), 'controller') === false) {
            return true;
        }

        return false;
    }

    /**
     * @param integer   $stackPtr
     * @param array     $tokens
     * @param integer   $nextToken
     *
     * @return bool
     */
    private function isController($stackPtr, array $tokens, $nextToken)
    {
        if ($tokens[$stackPtr]['code'] === T_CLASS
            && strpos(strtolower($tokens[$nextToken]['content']), 'controller') > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param integer   $stackPtr
     * @param array     $tokens
     * @param integer   $prevToken
     *
     * @return bool
     */
    private function isForbiddenFunction($stackPtr, array $tokens, $prevToken)
    {
        if ($this->isController !== true) {
            return false;
        }

        if ($tokens[$prevToken - 2]['code'] !== T_PUBLIC) {
            return false;
        }

        if (strpos(strtolower($tokens[$stackPtr]['content']), '__') !== false) {
            return false;
        }

        if (strpos(strtolower($tokens[$stackPtr]['content']), 'action') !== false) {
            return false;
        }

        return true;
    }
}
