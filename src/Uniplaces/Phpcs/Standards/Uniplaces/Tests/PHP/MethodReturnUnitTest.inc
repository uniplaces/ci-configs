<?php

class MethodReturn
{
    /**
     * @return string
     */
    public function returnOk()
    {
        $b;
        return '';
    }

    /**
     * @return string
     */
    public function returnOkWithComment()
    {
        // comment
        return '';
    }

    /**
     * @return string
     */
    public function returnWithMultipleReturnsAndSomeOkAndOthersNotOk()
    {
        $a = '';
        if ($a === false) {
            return '';
        }

        if ($a === '') {
            $b = $a;

            return $b;
        }

        if ($a === 'b') {
            $b = $a;
            return $b;
        }

        if ($a === 'c') {
            // comment
            return $a;
        }

        return $a;
    }

    /**
     * @return string
     */
    public function returnNotOkWithStatements()
    {
        $a = '';
        return $a;
    }

    /**
     * @return string
     */
    public function returnOkWithStatements()
    {
        $a = '';
        if ($a === false) {
            return '';
        }

        if ($a === '') {
            $b = $a;

            return $b;
        }

        if ($a === 'b') {
            $b = $a;

            return $b;
        }

        return $a;
    }

    /**
     * @return string
     */
    public function returnOkWithCoverageIgnoreComment()
    {
        // @codeCoverageIgnoreStart
        return '';
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return string
     */
    public function returnNotOkWithNewLineBetweenCommentAndReturnStatement()
    {
        if (true == 'c') {
            // comment
            return '';
        }

        if (true == 'c') {
            // comment

            return '';
        }

        if (true == 'c') {

            return '';
        }
    }

    /**
     * @return string
     */
    public function returnNotOkWithNewLineBeforeReturnStatement()
    {

        return '';
    }

    /**
     * @return string
     */
    public function returnNotOkWithMoreThanTwoNewLineBeforeReturnStatement()
    {
        // this is a comment
        $a = '';



        return $a;
    }

    /**
     * @return string
     */
    public function returnNotOkWithMoreThanTwoNewLineBeforeReturnStatement2()
    {
        if (true) {
            return '';
        }



        return $a;
    }
}
