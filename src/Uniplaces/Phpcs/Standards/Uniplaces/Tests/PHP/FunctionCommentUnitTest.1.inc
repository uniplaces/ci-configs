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
     * @param $text
     *
     * @return string
     */
    public function missingDocBlockParamType(string $text);

    /**
     * @param $text
     */
    public function missingDocBlockReturn(string $text): string;

    /**
     * @param bool $text
     *
     * @return string
     */
    public function mismatchParamDocBlockTypeAndParamTypeHint(string $text): string;

    /**
     * @param string $text
     *
     * @return string
     */
    public function mismatchReturnTypeHintAndDocBlockReturn(string $text): bool;

    /**
     * @param bool $text
     *
     * @return bool
     */
    public function mismatchReturnTypeHintAndScalarTypeHint(string $text): bool;
}
