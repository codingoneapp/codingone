<?php
Page::$need_view = false;
$path = 'upload/temp/'.md5(time().session_id());
Upload::save_upload_file($path);
