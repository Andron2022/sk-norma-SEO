<?php
/**
 * Simpla int modifier plugin
 *
 * Type:     modifier
 * Name:     int
 * Purpose:  Wrapper for the PHP 'intval' function
 */
function tpl_modifier_int($string)
{
    return intval($string);
}
?>