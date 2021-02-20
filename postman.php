<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

function depura($mixed_var){
    ?><PRE><DIV style="display: list-item;font-size: 14px; font-weight: bold;	color: #FF0000;	background-color: #FFFFCC; border: 1px dotted #000000;" align="left"><? print_r($mixed_var); ?></DIV></PRE><?
}

////////////////////////////////////////////////////////////////////////////////
depura(headers_list());
depura($_GET);
depura($_POST);
depura($_FILES);
?>