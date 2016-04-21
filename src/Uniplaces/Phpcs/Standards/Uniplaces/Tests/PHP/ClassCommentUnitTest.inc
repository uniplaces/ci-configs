<?php

class ClassCommentWithoutDocBlock
{
}

/**
 * ClassCommentWithCorrectDocBlock
 *
 * @method string __toString()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ClassCommentWithCorrectDocBlock
{
}

/*
 * ClassCommentWithWrongDocBlock
 */
class ClassCommentWithWrongDocBlock
{
}

/** ClassCommentWithInlineDocBlock */
class ClassCommentWithInlineDocBlock
{
}

// ClassCommentWithSingleComment
class ClassCommentWithSingleComment
{
}

/**
 * ClassCommentWithCorrectDocBlock
 *
 * @package
 * @author
 */
class ClassCommentWithNotAllowedTags
{
}

/**
 * ClassCommentWithNewLine
 *
 */

class ClassCommentWithNewLine
{
}
/**
 * ClassCommentWithNewLine
 *
 */
class ClassCommentWithoutNewLineBetweenClasses
{
}
