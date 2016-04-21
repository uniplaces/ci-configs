<?php

class ClassCommentWithoutDocblock
{
}

/**
 * ClassCommentWithCorrectDocblock
 *
 * @method string __toString()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ClassCommentWithCorrectDocblock
{
}

/*
 * ClassCommentWithWrongDocblock
 */
class ClassCommentWithWrongDocblock
{
}

/** ClassCommentWithInlineDocblock */
class ClassCommentWithInlineDocblock
{
}

// ClassCommentWithSingleComment
class ClassCommentWithSingleComment
{
}

/**
 * ClassCommentWithCorrectDocblock
 *
 * @package
 * @author
 */
class ClassCommentWithNotAllowedTags
{
}
