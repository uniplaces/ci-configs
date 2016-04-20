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
    public function returnOkWithStatements()
    {
        $a = '';

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
}
