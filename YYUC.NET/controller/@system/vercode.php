<?php
$ischinese = Request::get(2,'0');
$len = Request::get(1,'4');
Vercode::outputimg(intval($len),$ischinese=='1');
