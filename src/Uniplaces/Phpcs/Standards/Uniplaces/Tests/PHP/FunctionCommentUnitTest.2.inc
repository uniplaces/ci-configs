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
}
