<?php

/**
 * Class Uniplaces_Sniffs_PHP_ControllerFunctionsSniff
 */
class Uniplaces_Sniffs_PHP_ControllerFunctionsSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * @var bool
     */
    private $isController = false;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_CLASS, T_PUBLIC, T_STRING];
    }


    /**
     * @param PHP_CodeSniffer_File  $phpcsFile
     * @param array                 $tokens
     * @param integer               $prevToken
     *
     * @return bool
     */
    private function isNSSeparator(PHP_CodeSniffer_File $phpcsFile, array $tokens, $prevToken)
    {
        if ($tokens[$prevToken]['code'] === T_NS_SEPARATOR) {
            $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($prevToken - 1), null, true);
            if ($tokens[$prevToken]['code'] === T_STRING) {
                return true;
            }
        }

        return false;
    }





    /**
     * @param PHP_CodeSniffer_File  $phpcsFile
     * @param integer               $stackPtr
     * @return bool
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $function = $tokens[$stackPtr]['content'];

        $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        $this->toggleIsController($this->isController($stackPtr, $tokens, $nextToken));

        if (!$this->isForbiddenFunction($stackPtr, $tokens, $prevToken)) {
            return true;
        }

        return $phpcsFile->addError('The use of function %s is forbidden. Please move non Action methods into model', $stackPtr, 'Found', [$function]);
    }


    /**
     * @param $isController
     */
    public function toggleIsController($isController)
    {
        if ($this->isController === true) {
            return;
        }
        $this->isController = $isController;
    }

    /**
     * @param $stackPtr
     * @param array $tokens
     * @param $nextToken
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