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
}
