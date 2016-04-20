<?php

interface FunctionCommentInterface
{
    /**
     * @param string $text
     *
     * @return string
     */
    public function allOk(string $text): string;

    /**
     * @param string $text
     *
     * @return string
     */
    public function missingParamTypeHint($text): string;

    /**
     * @param string $text
     *
     * @return string
     */
    public function missingReturnTypeHint(string $text);

    /**
     * @param string $text
     *
     * @return string
     */
    public function missingReturnAndParamTypeHint($text);

    /**
     * @param string $text
     *
     * @return string
     */
    public function missingDifferentReturnTypeHintAndComment(string $text): bool;
}
