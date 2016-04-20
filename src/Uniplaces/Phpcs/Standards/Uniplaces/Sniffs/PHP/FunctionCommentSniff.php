<?php

/**
 * This file is part of Uniplaces ci-configs source.
 *
 * (c) Daniel Gomes <daniel@uniplaces.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (class_exists('PEAR_Sniffs_Commenting_FunctionCommentSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PEAR_Sniffs_Commenting_FunctionCommentSniff not found');
}

/**
 * Uniplaces_Sniffs_PHP_ClassCommentSniff
 *
 * @author Daniel Gomes <daniel@uniplaces.com>
 */
class Uniplaces_Sniffs_PHP_FunctionCommentSniff extends Squiz_Sniffs_Commenting_FunctionCommentSniff
{
    /**
     * An array of variable types for param/var we will check.
     *
     * @var array(string)
     */
    public static $allowedTypes = [
        'array',
        'bool',
        'float',
        'int',
        'mixed',
        'string',
        'resource',
        'callable',
    ];


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $find = PHP_CodeSniffer_Tokens::$methodPrefixes;
        $find[] = T_WHITESPACE;

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);
        // we don't want methods start with "test" to have doc blocks
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT
            && !strpos($phpcsFile->getDeclarationName($stackPtr), 'test')
        ) {
            return;
        }

        parent::process($phpcsFile, $stackPtr);
    }

    /**
     * Process the return comment of this function comment.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                  $stackPtr     The position of the current token
     *                                           in the stack passed in $tokens.
     * @param int                  $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processReturn(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        // Validates interfaces return type hint
        $interfaceToken = $phpcsFile->findPrevious(T_INTERFACE, $stackPtr);
        $isInterface = $interfaceToken !== false && $tokens[$interfaceToken]['code'] === T_INTERFACE;
        if ($isInterface) {
            $return = null;
            foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
                if ($tokens[$tag]['content'] === '@return') {
                    if ($return !== null) {
                        $error = 'Only 1 @return tag is allowed in a function comment';
                        $phpcsFile->addError($error, $tag, 'DuplicateReturn');

                        return;
                    }

                    $return = $tag;
                }
            }

            $content = $return !== null ? $tokens[($return + 2)]['content'] : false;
            if ($content !== false && $content !== 'mixed') {
                $typeNames = explode('|', $content);
                $suggestedNames = [];
                foreach ($typeNames as $i => $typeName) {
                    $suggestedName = static::suggestType($typeName);
                    if (in_array($suggestedName, $suggestedNames) === false) {
                        $suggestedNames[] = $suggestedName;
                    }
                }

                $suggestedType = implode('|', $suggestedNames);

                $interfaceMethodEntToken = $phpcsFile->findNext(
                    T_SEMICOLON,
                    $stackPtr
                );

                if (count($typeNames) > 1) {
                    return;
                }

                // If a return type hint does not exists
                $returnHint = $phpcsFile->findNext(T_RETURN_TYPE, $stackPtr, $interfaceMethodEntToken);
                $hasReturnHint = $returnHint !== false;

                if (count($typeNames) === 1 && !$hasReturnHint) {
                    $error = 'Missing return type hint';
                    $phpcsFile->addError($error, $stackPtr, 'NoReturnTypeHint');
                }

                if (count($typeNames) === 1 && $hasReturnHint && $tokens[$returnHint]['content'] !== $suggestedType) {
                    $error = 'Expected "%s" but the type is not the same or no return type found';
                    $data = [$suggestedType];
                    $phpcsFile->addError(
                        $error,
                        $stackPtr,
                        'InvalidReturnOrReturnTypeHint',
                        $data
                    );
                }

            }

            return;
        }

        // Only check for a return comment if a non-void return statement exists
        if (isset($tokens[$stackPtr]['scope_opener'])) {
            // Start inside the function
            $start = $phpcsFile->findNext(
                T_OPEN_CURLY_BRACKET,
                $stackPtr,
                $tokens[$stackPtr]['scope_closer']
            );
            for ($i = $start; $i < $tokens[$stackPtr]['scope_closer']; ++$i) {
                // Skip closures
                if ($tokens[$i]['code'] === T_CLOSURE) {
                    $i = $tokens[$i]['scope_closer'];
                    continue;
                }
                // Found a return not in a closure statement
                // Run the check on the first which is not only 'return;'
                if ($tokens[$i]['code'] === T_RETURN
                    && $this->isMatchingReturn($tokens, $i)
                ) {
                    $methodName = $phpcsFile->getDeclarationName($phpcsFile->findPrevious(T_FUNCTION, $stackPtr));

                    // Skip constructor and destructor.
                    if ($methodName === '__construct' || $methodName === '__destruct') {
                        return;
                    }

                    $endToken = isset($tokens[$stackPtr]['scope_closer']) ? $tokens[$stackPtr]['scope_closer'] : false;
                    $returnToken = $phpcsFile->findNext(T_RETURN, $stackPtr, $endToken);
                    $parenthesesCloserBeforeCurlyOpenToken = $phpcsFile->findNext(
                        T_CLOSE_PARENTHESIS,
                        $stackPtr-1,
                        $endToken
                    );

                    // verify if a return type hint's present when a return statement exists when docblock is inheritdoc
                    if ($this->isInheritDoc($phpcsFile, $stackPtr)) {
                        // If return type is not void, there needs to be a return statement
                        // somewhere in the function that returns something.
                        if ($endToken !== false) {
                            $returnHint = $phpcsFile->findNext(T_RETURN_TYPE, $stackPtr, $endToken);
                            if ($returnToken !== false && $returnHint === false) {
                                $warning = 'Function return type hint is missing?';
                                $phpcsFile->addWarning(
                                    $warning,
                                    $parenthesesCloserBeforeCurlyOpenToken,
                                    'MissingReturnTypeHintWithInheritdoc'
                                );
                            }
                        }

                        return;
                    }

                    $return = null;
                    foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
                        if ($tokens[$tag]['content'] === '@return') {
                            if ($return !== null) {
                                $error = 'Only 1 @return tag is allowed in a function comment';
                                $phpcsFile->addError($error, $tag, 'DuplicateReturn');

                                return;
                            }

                            $return = $tag;
                        }
                    }

                    if ($return !== null) {
                        $content = $tokens[($return + 2)]['content'];
                        if (empty($content) === true || $tokens[($return + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                            $error = 'Return type missing for @return tag in function comment';
                            $phpcsFile->addError($error, $return, 'MissingReturnType');
                        } else {
                            // Check return type (can be multiple, separated by '|').
                            $typeNames = explode('|', $content);
                            $suggestedNames = [];
                            foreach ($typeNames as $i => $typeName) {
                                $suggestedName = static::suggestType($typeName);
                                if (in_array($suggestedName, $suggestedNames) === false) {
                                    $suggestedNames[] = $suggestedName;
                                }
                            }

                            $suggestedType = implode('|', $suggestedNames);
                            if ($content !== $suggestedType) {
                                $error = 'Expected "%s" but found "%s" for function return type';
                                $data = [
                                    $suggestedType,
                                    $content,
                                ];
                                $fix = $phpcsFile->addError($error, $return, 'InvalidReturn', $data);
                                if ($fix === true) {
                                    $phpcsFile->fixer->replaceToken(($return + 2), $suggestedType);
                                }
                            }

                            // If the return type is void, make sure there is
                            // no return statement in the function.
                            if ($content === 'void') {
                                if (isset($tokens[$stackPtr]['scope_closer']) === true) {
                                    $endToken = $tokens[$stackPtr]['scope_closer'];
                                    for ($returnToken = $stackPtr; $returnToken < $endToken; $returnToken++) {
                                        if ($tokens[$returnToken]['code'] === T_CLOSURE) {
                                            $returnToken = $tokens[$returnToken]['scope_closer'];
                                            continue;
                                        }

                                        if ($tokens[$returnToken]['code'] === T_RETURN
                                            || $tokens[$returnToken]['code'] === T_YIELD
                                        ) {
                                            break;
                                        }
                                    }

                                    if ($returnToken !== $endToken) {
                                        // If the function is not returning anything, just
                                        // exiting, then there is no problem.
                                        $semicolon = $phpcsFile->findNext(T_WHITESPACE, ($returnToken + 1), null, true);
                                        if ($tokens[$semicolon]['code'] !== T_SEMICOLON) {
                                            $error = 'Function return type is void, but function contains return statement';
                                            $phpcsFile->addError($error, $return, 'InvalidReturnVoid');
                                        }
                                    }
                                }
                            } else {
                                if ($content !== 'mixed') {
                                    // If a return type hint does not exists
                                    $returnHint = $phpcsFile->findNext(T_RETURN_TYPE, $stackPtr, $endToken);
                                    $hasReturnHint = $returnHint !== false;
                                    if (count($typeNames) === 1 && !$hasReturnHint) {
                                        $error = 'Missing return type hint';
                                        $phpcsFile->addError($error, $parenthesesCloserBeforeCurlyOpenToken, 'NoReturnTypeHint');
                                    }

                                    if (count($typeNames) === 1 && $hasReturnHint && $tokens[$returnHint]['content'] !== $suggestedType) {
                                        $error = 'Expected "%s" but the type is not the same or no return type found';
                                        $data = [$suggestedType];
                                        $phpcsFile->addError($error, $parenthesesCloserBeforeCurlyOpenToken, 'InvalidReturnOrReturnTypeHint', $data);
                                    }

                                    // If return type is not void, there needs to be a return statement
                                    // somewhere in the function that returns something.
                                    if (isset($tokens[$stackPtr]['scope_closer']) === true) {
                                        if ($returnToken === false) {
                                            $error = 'Function return type is not void, but function has no return statement';
                                            $phpcsFile->addError($error, $return, 'InvalidNoReturn');
                                        } else {
                                            $semicolon = $phpcsFile->findNext(
                                                T_WHITESPACE,
                                                ($returnToken + 1),
                                                null,
                                                true
                                            );

                                            if ($tokens[$semicolon]['code'] === T_SEMICOLON) {
                                                $error = 'Function return type is not void, but function is returning void here';
                                                $phpcsFile->addError($error, $returnToken, 'InvalidReturnNotVoid');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $error = 'Missing @return tag in function comment';
                        $phpcsFile->addError($error, $tokens[$commentStart]['comment_closer'], 'MissingReturn');
                    }
                    break;
                }
            }
        }
    }

    /**
     * Is the comment an inheritdoc?
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return boolean True if the comment is an inheritdoc
     */
    protected function isInheritDoc(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $start = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, $stackPtr - 1);
        $end = $phpcsFile->findNext(T_DOC_COMMENT_CLOSE_TAG, $start);
        $content = $phpcsFile->getTokensAsString($start, ($end - $start));

        return preg_match('#{@inheritdoc}#i', $content) === 1;
    }

    /**
     * Process any throw tags that this function comment has.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                  $stackPtr     The position of the current token
     *                                           in the stack passed in $tokens.
     * @param int                  $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processThrows(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $commentStart)
    {
        if ($this->isInheritDoc($phpcsFile, $stackPtr)) {
            return;
        }

        parent::processThrows($phpcsFile, $stackPtr, $commentStart);
    }

    /**
     * Process the function parameter comments.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                  $stackPtr     The position of the current token
     *                                           in the stack passed in $tokens.
     * @param int                  $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processParams(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $commentStart)
    {
        if ($this->isInheritDoc($phpcsFile, $stackPtr)) {
            return;
        }
        $tokens = $phpcsFile->getTokens();

        $params = [];
        $maxType = 0;
        $maxVar = 0;
        foreach ($tokens[$commentStart]['comment_tags'] as $pos => $tag) {
            if ($tokens[$tag]['content'] !== '@param') {
                continue;
            }

            $type = '';
            $typeSpace = 0;
            $var = '';
            $varSpace = 0;
            $comment = '';
            $commentLines = [];
            if ($tokens[($tag + 2)]['code'] === T_DOC_COMMENT_STRING) {
                $matches = [];
                preg_match('/([^$&]+)(?:((?:\$|&)[^\s]+)(?:(\s+)(.*))?)?/', $tokens[($tag + 2)]['content'], $matches);

                $typeLen = strlen($matches[1]);
                $type = trim($matches[1]);
                $typeSpace = ($typeLen - strlen($type));
                $typeLen = strlen($type);
                if ($typeLen > $maxType) {
                    $maxType = $typeLen;
                }

                if (isset($matches[2]) === true) {
                    $var = $matches[2];
                    $varLen = strlen($var);
                    if ($varLen > $maxVar) {
                        $maxVar = $varLen;
                    }

                    if (isset($matches[4]) === true) {
                        $varSpace = strlen($matches[3]);
                        $comment = $matches[4];
                        $commentLines[] = [
                            'comment' => $comment,
                            'token' => ($tag + 2),
                            'indent' => $varSpace,
                        ];

                        // Any strings until the next tag belong to this comment.
                        if (isset($tokens[$commentStart]['comment_tags'][($pos + 1)]) === true) {
                            $end = $tokens[$commentStart]['comment_tags'][($pos + 1)];
                        } else {
                            $end = $tokens[$commentStart]['comment_closer'];
                        }

                        for ($i = ($tag + 3); $i < $end; $i++) {
                            if ($tokens[$i]['code'] === T_DOC_COMMENT_STRING) {
                                $indent = 0;
                                if ($tokens[($i - 1)]['code'] === T_DOC_COMMENT_WHITESPACE) {
                                    $indent = strlen($tokens[($i - 1)]['content']);
                                }

                                $comment .= ' ' . $tokens[$i]['content'];
                                $commentLines[] = [
                                    'comment' => $tokens[$i]['content'],
                                    'token' => $i,
                                    'indent' => $indent,
                                ];
                            }
                        }
                    } else {
                        $error = 'Missing parameter comment';
                        $phpcsFile->addError($error, $tag, 'MissingParamComment');
                        $commentLines[] = ['comment' => ''];
                    }
                } else {
                    $error = 'Missing parameter name';
                    $phpcsFile->addError($error, $tag, 'MissingParamName');
                }
            } else {
                $error = 'Missing parameter type';
                $phpcsFile->addError($error, $tag, 'MissingParamType');
            }

            $params[] = [
                'tag' => $tag,
                'type' => $type,
                'var' => $var,
                'comment' => $comment,
                'commentLines' => $commentLines,
                'type_space' => $typeSpace,
                'var_space' => $varSpace,
            ];
        }

        $realParams = $phpcsFile->getMethodParameters($stackPtr);
        $foundParams = [];

        foreach ($params as $pos => $param) {
            // If the type is empty, the whole line is empty.
            if ($param['type'] === '') {
                continue;
            }

            // Check the param type value.
            $typeNames = explode('|', $param['type']);
            foreach ($typeNames as $typeName) {
                $suggestedName = static::suggestType($typeName);

                if ($typeName !== $suggestedName) {
                    $error = 'Expected "%s" but found "%s" for parameter type';
                    $data = [
                        $suggestedName,
                        $typeName,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $param['tag'], 'IncorrectParamVarName', $data);
                    if ($fix === true) {
                        $content = $suggestedName;
                        $content .= str_repeat(' ', $param['type_space']);
                        $content .= $param['var'];
                        $content .= str_repeat(' ', $param['var_space']);
                        if (isset($param['commentLines'][0]) === true) {
                            $content .= $param['commentLines'][0]['comment'];
                        }

                        $phpcsFile->fixer->replaceToken(($param['tag'] + 2), $content);
                    }
                } else {
                    if (count($typeNames) === 1) {
                        // Check type hint for array and custom type.
                        if (strpos($suggestedName, 'array') !== false) {
                            $suggestedTypeHint = 'array';
                        } else {
                            if (strpos($suggestedName, 'callable') !== false) {
                                $suggestedTypeHint = 'callable';
                            } else {
                                $suggestedTypeHint = $suggestedName;
                            }
                        }

                        if ($suggestedTypeHint !== '' && isset($realParams[$pos]) === true) {
                            $typeHint = $realParams[$pos]['type_hint'];
                            if ($typeHint === '') {
                                $error = 'Type hint "%s" missing for %s';
                                $data = [
                                    $suggestedTypeHint,
                                    $param['var'],
                                ];
                                $phpcsFile->addError($error, $stackPtr, 'TypeHintMissing', $data);
                            } else {
                                if ($typeHint !== substr($suggestedTypeHint, (strlen($typeHint) * -1))) {
                                    $error = 'Expected type hint "%s"; found "%s" for %s';
                                    $data = [
                                        $suggestedTypeHint,
                                        $typeHint,
                                        $param['var'],
                                    ];
                                    $phpcsFile->addError($error, $stackPtr, 'IncorrectTypeHint', $data);
                                }
                            }
                        } else {
                            if ($suggestedTypeHint === '' && isset($realParams[$pos]) === true) {
                                $typeHint = $realParams[$pos]['type_hint'];
                                if ($typeHint !== '') {
                                    $error = 'Unknown type hint "%s" found for %s';
                                    $data = [
                                        $typeHint,
                                        $param['var'],
                                    ];
                                    $phpcsFile->addError($error, $stackPtr, 'InvalidTypeHint', $data);
                                }
                            }
                        }
                    }
                }
            }

            if ($param['var'] === '') {
                continue;
            }

            $foundParams[] = $param['var'];

            // Check number of spaces after the type.
            $spaces = ($maxType - strlen($param['type']) + 1);
            if ($param['type_space'] !== $spaces) {
                $error = 'Expected %s spaces after parameter type; %s found';
                $data = [
                    $spaces,
                    $param['type_space'],
                ];

                $fix = $phpcsFile->addFixableError($error, $param['tag'], 'SpacingAfterParamType', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();

                    $content = $param['type'];
                    $content .= str_repeat(' ', $spaces);
                    $content .= $param['var'];
                    $content .= str_repeat(' ', $param['var_space']);
                    $content .= $param['commentLines'][0]['comment'];
                    $phpcsFile->fixer->replaceToken(($param['tag'] + 2), $content);

                    // Fix up the indent of additional comment lines.
                    foreach ($param['commentLines'] as $lineNum => $line) {
                        if ($lineNum === 0
                            || $param['commentLines'][$lineNum]['indent'] === 0
                        ) {
                            continue;
                        }

                        $newIndent = ($param['commentLines'][$lineNum]['indent'] + $spaces - $param['type_space']);
                        $phpcsFile->fixer->replaceToken(
                            ($param['commentLines'][$lineNum]['token'] - 1),
                            str_repeat(' ', $newIndent)
                        );
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }

            // Make sure the param name is correct.
            if (isset($realParams[$pos]) === true) {
                $realName = $realParams[$pos]['name'];
                if ($realName !== $param['var']) {
                    $code = 'ParamNameNoMatch';
                    $data = [
                        $param['var'],
                        $realName,
                    ];

                    $error = 'Doc comment for parameter %s does not match ';
                    if (strtolower($param['var']) === strtolower($realName)) {
                        $error .= 'case of ';
                        $code = 'ParamNameNoCaseMatch';
                    }

                    $error .= 'actual variable name %s';

                    $phpcsFile->addError($error, $param['tag'], $code, $data);
                }
            } else {
                if (substr($param['var'], -4) !== ',...') {
                    // We must have an extra parameter comment.
                    $error = 'Superfluous parameter comment';
                    $phpcsFile->addError($error, $param['tag'], 'ExtraParamComment');
                }
            }

            if ($param['comment'] === '') {
                continue;
            }

            // Check number of spaces after the var name.
            $spaces = ($maxVar - strlen($param['var']) + 1);
            if ($param['var_space'] !== $spaces) {
                $error = 'Expected %s spaces after parameter name; %s found';
                $data = [
                    $spaces,
                    $param['var_space'],
                ];

                $fix = $phpcsFile->addFixableError($error, $param['tag'], 'SpacingAfterParamName', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();

                    $content = $param['type'];
                    $content .= str_repeat(' ', $param['type_space']);
                    $content .= $param['var'];
                    $content .= str_repeat(' ', $spaces);
                    $content .= $param['commentLines'][0]['comment'];
                    $phpcsFile->fixer->replaceToken(($param['tag'] + 2), $content);

                    // Fix up the indent of additional comment lines.
                    foreach ($param['commentLines'] as $lineNum => $line) {
                        if ($lineNum === 0
                            || $param['commentLines'][$lineNum]['indent'] === 0
                        ) {
                            continue;
                        }

                        $newIndent = ($param['commentLines'][$lineNum]['indent'] + $spaces - $param['var_space']);
                        $phpcsFile->fixer->replaceToken(
                            ($param['commentLines'][$lineNum]['token'] - 1),
                            str_repeat(' ', $newIndent)
                        );
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }

            // Param comments must start with a capital letter and end with the full stop.
            $firstChar = $param['comment']{0};
            if (preg_match('|\p{Lu}|u', $firstChar) === 0) {
                $error = 'Parameter comment must start with a capital letter';
                $phpcsFile->addError($error, $param['tag'], 'ParamCommentNotCapital');
            }

            $lastChar = substr($param['comment'], -1);
            if ($lastChar !== '.') {
                $error = 'Parameter comment must end with a full stop';
                $phpcsFile->addError($error, $param['tag'], 'ParamCommentFullStop');
            }
        }

        $realNames = [];
        foreach ($realParams as $realParam) {
            $realNames[] = $realParam['name'];
        }

        // Report missing comments.
        $diff = array_diff($realNames, $foundParams);
        foreach ($diff as $neededParam) {
            $error = 'Doc comment for parameter "%s" missing';
            $data = [$neededParam];
            $phpcsFile->addError($error, $commentStart, 'MissingParamTag', $data);
        }
    }

    /**
     * Is the return statement matching?
     *
     * @param array $tokens    Array of tokens
     * @param int   $returnPos Stack position of the T_RETURN token to process
     *
     * @return boolean True if the return does not return anything
     */
    protected function isMatchingReturn($tokens, $returnPos)
    {
        do {
            $returnPos++;
        } while ($tokens[$returnPos]['code'] === T_WHITESPACE);

        return $tokens[$returnPos]['code'] !== T_SEMICOLON;
    }

    /**
     * Returns a valid variable type for param/var tag.
     *
     * If type is not one of the standard type, it must be a custom type.
     * Returns the correct type name suggestion if type name is invalid.
     *
     * @param string $varType The variable type to process.
     *
     * @return string
     */
    public static function suggestType($varType)
    {
        if ($varType === '') {
            return '';
        }

        if (in_array($varType, static::$allowedTypes) === true) {
            return $varType;
        } else {
            $lowerVarType = strtolower($varType);
            switch ($lowerVarType) {
                case 'boolean':
                    return 'bool';
                case 'double':
                case 'real':
                    return 'float';
                case 'integer':
                    return 'int';
                case 'array()':
                    return 'array';
            }//end switch

            if (strpos($lowerVarType, 'array(') !== false) {
                // Valid array declaration:
                // array, array(type), array(type1 => type2).
                $matches = [];
                $pattern = '/^array\(\s*([^\s^=^>]*)(\s*=>\s*(.*))?\s*\)/i';
                if (preg_match($pattern, $varType, $matches) !== 0) {
                    $type1 = '';
                    if (isset($matches[1]) === true) {
                        $type1 = $matches[1];
                    }

                    $type2 = '';
                    if (isset($matches[3]) === true) {
                        $type2 = $matches[3];
                    }

                    $type1 = static::suggestType($type1);
                    $type2 = static::suggestType($type2);
                    if ($type2 !== '') {
                        $type2 = ' => ' . $type2;
                    }

                    return "array($type1$type2)";
                } else {
                    return 'array';
                }
            } else {
                if (in_array($lowerVarType, static::$allowedTypes) === true) {
                    // A valid type, but not lower cased.
                    return $lowerVarType;
                } else {
                    // Must be a custom type name.
                    return $varType;
                }
            }
        }

    }
}

