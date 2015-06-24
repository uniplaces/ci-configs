<?php

/**
 * Checks code for forbidden occurence of debug methods
 *
 * Inspired by https://github.com/typo3-ci/TYPO3SniffPool/blob/develop/Sniffs/Debug/DebugCodeSniff.php
 *
 * Class Uniplaces_Sniffs_Debug_DebugCodeSniff
 */
class Uniplaces_Sniffs_Debug_DebugCodeSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['PHP'];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_STRING,
            T_COMMENT
        ];

    }


    /**
     * @param PHP_CodeSniffer_File  $phpcsFile
     * @param int                   $stackPtr
     *
     * @return bool
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $code = $error = $errorData = null;
        $tokens = $phpcsFile->getTokens();
        $tokenType = $tokens[$stackPtr]['type'];
        $currentToken = $tokens[$stackPtr]['content'];

        switch ($tokenType) {
            case 'T_STRING':
                list($error, $stackPtr, $code, $errorData) = $this->handleTString(
                    $phpcsFile, $stackPtr, $tokens,  $currentToken );
                break;
            case 'T_COMMENT':
                list($error, $stackPtr, $code, $errorData) = $this->handleTComment(
                    $stackPtr, $currentToken);
                break;
            default:
        }

        if ($error) {
            return $phpcsFile->addError($error, $stackPtr, $code, $errorData);
        }

        return true;
    }

    /**
     * @param int       $stackPtr
     * @param string    $currentToken
     *
     * @return array
     */
    private function handleTComment($stackPtr, $currentToken)
    {
        $hasDebugInComment = preg_match_all(
            '/\b((DebugUtility::)?([x]?debug)|(print_r)|(var_dump))([\s]+)?\(/', $currentToken);
        if ($hasDebugInComment) {
            return [
                'Its not enough to comment out debug functions calls, they must be removed from code.',
                $stackPtr,
                'CommentOutDebugCall',
                []
            ];
        }

        return [null, null, null, null];
    }

    /**
     * @param PHP_CodeSniffer_File  $phpcsFile
     * @param int                   $stackPtr
     * @param array                 $tokens
     * @param string                $currentToken
     *
     * @return array
     */
    private function handleTString(PHP_CodeSniffer_File $phpcsFile, $stackPtr, array $tokens, $currentToken)
    {
        if ($currentToken === 'debug') {
            return $this->handleDebugToken($phpcsFile, $stackPtr, $tokens, $currentToken);
        }

        if (in_array($currentToken, ['print_r', 'var_dump', 'xdebug'], true)) {
            return [
                'Call to debug function %s() must be removed',
                $stackPtr,
                'NativDebugFunction',
                [$currentToken]
            ];
        }

        return [null, null, null, null];
    }

    /**
     * @param PHP_CodeSniffer_File  $phpcsFile
     * @param int                   $stackPtr
     * @param array                 $tokens
     * @param string                $currentToken
     *
     * @return array
     */
    private function handleDebugToken(PHP_CodeSniffer_File $phpcsFile, $stackPtr, array $tokens, $currentToken)
    {
        $previousToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);

        if ($tokens[$previousToken]['content'] === '::') {
            return [
                'Call to debug function %s::debug() must be removed',
                $stackPtr,
                'StaticDebugCall',
                [$tokens[($previousToken - 1)]['content']]
            ];
        }

        if (in_array($tokens[$previousToken]['content'], ['->', 'class', 'function'], true)) {
            return [null, null, null, null];
        }

        return [
            'Call to debug function %s() must be removed',
            $stackPtr,
            'DebugFunctionCall',
            [$currentToken]
        ];
    }


}