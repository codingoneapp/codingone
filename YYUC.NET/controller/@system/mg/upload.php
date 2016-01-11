<?php
page::ignore_view();
$path = 'upload/temp/'.md5(time().session_id());
Upload::save_upload_file($path,null);
?>