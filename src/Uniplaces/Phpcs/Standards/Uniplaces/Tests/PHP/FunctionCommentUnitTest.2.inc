<?php

class FunctionComment
{
    /**
     * @param string $text
     *
     * @return string
     */
    public function allOk(string $text): string
    {
        return $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function missingParamTypeHint($text): string
    {
        return $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function missingReturnTypeHint(string $text)
    {
        return $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function missingReturnAndParamTypeHint($text)
    {
        return $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function missingDifferentReturnTypeHintAndComment(string $text): bool
    {
        return (bool) $text;
    }

    /**
     * {@inheritdoc}
     */
    public function missingReturnTypeHintWithInheritdoc($text)
    {
        return $text;
    }

    /**
     * {@inheritdoc}
     */
    public function allOkWithInheritdoc($text): string
    {
        return $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function missingParamTypeHint($text): string
    {
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function missingReturnTypeHint(string $text)
    {
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function missingReturnAndParamTypeHint($text)
    {
        return $text;
    }

    /**
     * @param $text
     *
     * @return string
     */
    public function missingDocBlockParamType(string $text)
    {
    }

    /**
     * @param $text
     */
    public function missingDocBlockReturn(string $text): string
    {
    }

    /**
     * @param bool $text
     *
     * @return string
     */
    public function mismatchParamDocBlockTypeAndParamTypeHint(string $text): string
    {
        // comment
        return $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function mismatchReturnTypeHintAndDocBlockReturn(string $text): bool
    {
        return $text;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function validate()
    {
        $array = [];
        if ($array == []) {
            throw new \InvalidArgumentException('You must provide at least one email address');
        }
    }

    /**
     * @return string
     * @return string
     */
    public function doubleReturnTags(): string
    {
        return '';
    }

    /**
     * @param integer $a
     *
     * @return integer
     */
    public function returnIntegerInsteadOfInt(integer $a): integer
    {
        return $a;
    }

    /**
     * @param integer $a
     */
    public function paramIntegerInsteadOfInt(integer $a)
    {
    }

    /**
     * @param int     $a    Comment.
     */
    public function paramTypeTooManySpacesInsteadOfInt(int $a)
    {
    }

    /**
     * @param int $b Comment.
     */
    public function paramTypeNameMismatchParamArgument(int $a)
    {
    }

    /**
     * @param int $b comment.
     */
    public function paramTypeCommnetLowercase(int $b)
    {
    }

    /**
     * @param int $b Comment
     */
    public function paramTypeCommnetWithoutEndPeriod(int $b)
    {
    }

    /**
     * @param boolean $a
     * @param double  $b
     * @param array() $d
     */
    public function paramTypeSuggestions(boolean $a, double $b, array $d)
    {
    }

    /**
     * @param \Exception $e
     */
    public function paramTypeClass(\Exception $e)
    {
    }

    /**
     * @param
     */
    public function MissingParamDataTag(string $e)
    {
    }

    /**
     * @return string
     */
    public function MissingParamTag(string $e): string
    {
        return '';
    }
}
