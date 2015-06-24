<?php

/**
 * Class Uniplaces_Sniffs_PHP_ForbiddenFunctionsSniff
 */
// @codingStandardsIgnoreStart
class Uniplaces_Sniffs_PHP_ForbiddenFunctionsSniff implements PHP_CodeSniffer_Sniff
{
// @codingStandardsIgnoreEnd

    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array(string => string|null)
     */
    public $functions = [
        'sizeof' => 'count',
        'delete' => 'unset',
        'sprintf' => 'Uniplaces\Stringy\S::render',
        'getRequest' => '$request as method parameter'
    ];

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return $this->handleRegisterOnNonPatternMatch($this->functions);
    }

    /**
     * @param array $functions
     *
     * @return array
     */
    private function handleRegisterOnNonPatternMatch(array $functions)
    {
        $string = '<?php ';
        $functionsNames = array_keys($functions);
        foreach ($functionsNames as $name) {
            $string .= $name . '();';
        }

        $register = [];
        $tokens = token_get_all($string);
        array_shift($tokens);

        foreach ($tokens as $token) {
            if (is_array($token) === true) {
                $register[] = $token[0];
            }
        }

        return array_unique($register);
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
     * @param array     $ignore
     * @param array     $tokens
     * @param integer   $prevToken
     * @param integer   $nextToken
     *
     * @return bool
     */
    private function isPHPFunction($stackPtr, array $ignore, array $tokens, $prevToken, $nextToken)
    {
        if (isset($ignore[$tokens[$prevToken]['code']]) === true
            || isset($ignore[$tokens[$nextToken]['code']]) === true
            || ($tokens[$stackPtr]['code'] === T_STRING
                && $tokens[$nextToken]['code'] !== T_OPEN_PARENTHESIS)) {
            return false;
        }

        return true;
    }

    /**
     * @param array     $tokens
     * @param integer   $prevToken
     *
     * @return bool
     */
    private function isNonPHPFunction(array $tokens, $prevToken)
    {
        if (in_array($tokens[$prevToken]['content'], ['->', '::'], true) ===  true) {
            return true;
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

        $function = strtolower($tokens[$stackPtr]['content']);
        $functions = array_change_key_case($this->functions, CASE_LOWER);

        $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        if (array_key_exists($function, $functions) === false) {
            return true;
        }

        list($nsSeparator, $isPHPFunction, $isNonPHPFunction) = $this->getConditions(
            $phpcsFile,
            $stackPtr,
            $tokens,
            $prevToken,
            $nextToken
        );

        if ($nsSeparator === true || ($isPHPFunction === false && $isNonPHPFunction === false)) {
            return true;
        }

        return $this->notify($phpcsFile, $stackPtr, $function);
    }


    /**
     * @param PHP_CodeSniffer_File  $phpcsFile
     * @param integer               $stackPtr
     * @param array                 $tokens
     * @param integer               $prevToken
     * @param integer               $nextToken
     *
     * @return array
     */
    private function getConditions(PHP_CodeSniffer_File $phpcsFile, $stackPtr, array $tokens, $prevToken, $nextToken)
    {
        $nsSeparator = $this->isNSSeparator($phpcsFile, $tokens, $prevToken);
        $isNonPHPFunction = $this->isNonPHPFunction($tokens, $prevToken);
        $isPHPFunction = $this->isPHPFunction(
            $stackPtr,
            $this->getIgnoreTokenizers(),
            $tokens,
            $prevToken,
            $nextToken
        );

        return [$nsSeparator, $isPHPFunction, $isNonPHPFunction];
    }


    /**
     * @return array
     */
    private function getIgnoreTokenizers()
    {
        return [
            T_DOUBLE_COLON    => true,
            T_OBJECT_OPERATOR => true,
            T_FUNCTION        => true,
            T_CONST           => true,
            T_PUBLIC          => true,
            T_PRIVATE         => true,
            T_PROTECTED       => true,
            T_AS              => true,
            T_NEW             => true,
            T_INSTEADOF       => true,
            T_NS_SEPARATOR    => true,
            T_IMPLEMENTS      => true
        ];
    }

    /**
     * @param PHP_CodeSniffer_File  $phpcsFile
     * @param string                $stackPtr
     * @param string                $function
     *
     * @return bool
     */
    protected function notify(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $function)
    {
        $functions = array_change_key_case($this->functions, CASE_LOWER);
        return $this->addError($phpcsFile, $stackPtr, [$function, $functions[$function]]);
    }

    /**
     * @param PHP_CodeSniffer_File  $phpcsFile
     * @param integer               $stackPtr
     * @param array                 $data
     *
     * @return bool
     */
    private function addError(PHP_CodeSniffer_File $phpcsFile, $stackPtr, array $data)
    {
        return $phpcsFile->addError('The use of function %s is forbidden. Use %s instead', $stackPtr, 'Found', $data);
    }
}
