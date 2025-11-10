<?php
function required($v)
{
    return isset($v) && trim($v) !== '';
}
function email($v)
{
    return filter_var($v, FILTER_VALIDATE_EMAIL);
}
