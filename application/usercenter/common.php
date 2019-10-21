<?php
function replace_phone($phone){
    $reg = '/^(\d{3})\d{4}(\d{4})$/';
    return preg_replace($reg,'$1****$2',$phone);
}