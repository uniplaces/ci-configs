<?php
/**
 * This file is part of Uniplaces ci-configs source.
 *
 * (c) Daniel Gomes <daniel@uniplaces.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * Uniplaces_Sniffs_PHP_MethodReturnSniff
 *
 * @author Daniel Gomes <daniel@uniplaces.com>
 */
class Uniplaces_Sniffs_PHP_MethodReturnSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{

    public function __construct()
    {
        parent::__construct(array(T_CLASS), array(T_FUNCTION));
    }

    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     * @param int                  $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null) {
            // Ignore closures.
            return;
        }

        $endToken = $tokens[$stackPtr]['scope_closer'];
        $returnToken = $phpcsFile->findNext(T_RETURN, $stackPtr, $endToken);

        while ($returnToken !== false) {
            $returnTokenLine = $tokens[$returnToken]['line'];
            $returnPreviousOpenCurlyBracket = $phpcsFile->findPrevious(T_OPEN_CURLY_BRACKET, $returnToken);
            $returnPreviousOpenCurlyBracketLine = $tokens[$returnPreviousOpenCurlyBracket]['line'];
            $returnPreviousClosedCurlyBracket = $phpcsFile->findPrevious(T_CLOSE_CURLY_BRACKET, $returnToken);
            $returnPreviousClosedCurlyBracketLine = $returnPreviousClosedCurlyBracket !== false ? $tokens[$returnPreviousClosedCurlyBracket]['line'] : false;
            $returnPreviousSemicolon = $phpcsFile->findNext(T_SEMICOLON, $returnPreviousOpenCurlyBracket, $returnToken);
            $returnPreviousComment = $phpcsFile->findPrevious(T_COMMENT, $returnToken);
            $returnPreviousCommentLine = $returnPreviousComment === false ? false : $tokens[$returnPreviousComment]['line'];
            $commentBetweenOpenCurlyAndReturn = $returnPreviousCommentLine !== false && $returnPreviousOpenCurlyBracketLine < $returnPreviousCommentLine && $returnPreviousCommentLine < $returnTokenLine;
            $returnTokenLine = $tokens[$returnToken]['line'];

            if ($returnPreviousSemicolon === false && $returnTokenLine - $returnPreviousOpenCurlyBracketLine > 2 && $commentBetweenOpenCurlyAndReturn) {
                $error = 'Return statement MUST not have a new line before';
                $phpcsFile->addError($error, $returnToken, 'NoNewLineBetweenCommentAndReturnStatement');
            } elseif ($returnPreviousSemicolon === false && $returnTokenLine - $returnPreviousOpenCurlyBracketLine > 1 && !$commentBetweenOpenCurlyAndReturn) {
                $error = 'Return statement MUST not have a new line before';
                $phpcsFile->addError($error, $returnToken, 'ReturnStatementNoNewLine');
            }

            if ($returnPreviousSemicolon !== false) {
                $returnPreviousSemicolonLine = $tokens[$returnPreviousSemicolon]['line'];

                if ($returnPreviousSemicolonLine < $returnPreviousOpenCurlyBracketLine && $returnTokenLine - $returnPreviousOpenCurlyBracketLine > 1) {
                    $error = 'Return statement MUST not have a new line before';
                    $phpcsFile->addError($error, $returnToken, 'ReturnStatementNoNewLine');
                }

                if ($returnPreviousSemicolonLine > $returnPreviousOpenCurlyBracketLine && $returnTokenLine - $returnPreviousSemicolonLine === 1) {
                    $error = 'Missing new line before return statement';
                    $phpcsFile->addError($error, $returnToken, 'ReturnStatementMissingNewLine');
                }

                if ($returnPreviousSemicolonLine > $returnPreviousClosedCurlyBracketLine && $returnTokenLine - $returnPreviousSemicolonLine > 2) {
                    $error = 'Found %s new lines when expecting only 1 before return statement';
                    $data = [$returnTokenLine - $returnPreviousSemicolonLine-1];
                    $phpcsFile->addError($error, $returnToken, 'NewLinesBeforeReturnStatementExceeded', $data);
                }

                if ($returnPreviousClosedCurlyBracketLine > $returnPreviousSemicolonLine && $returnTokenLine - $returnPreviousClosedCurlyBracketLine > 2) {
                    $error = 'Found %s new lines when expecting only 1 before return statement';
                    $data = [$returnTokenLine - $returnPreviousClosedCurlyBracketLine-1];
                    $phpcsFile->addError($error, $returnToken, 'NewLinesBeforeReturnStatementExceeded', $data);
                }
            }

            $returnToken = $phpcsFile->findNext(T_RETURN, $returnToken+2, $endToken);
        }
    }
}
