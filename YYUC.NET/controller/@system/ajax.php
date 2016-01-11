<?php
if('dbuniquecheck'==Request::get(1)){
	$dbdate = Request::json();
	$m = new Model($dbdate[1]);
	$query_arr = array($dbdate[2]=>$dbdate[4]);
	if(trim($dbdate[3])!=''){
		$query_arr['id@<>'] = trim($dbdate[3]);
	}
	if($m->has($query_arr)){
		Response::write('no');
	}else{
		Response::write('ok');
	}
}elseif('getselvt'==Request::get(1)){
	$tn = String::decryption(Request::post('tn'));
	$aw = String::decryption(Request::post('aw'));
	$m = new Model($tn);
	$array = $m->field('id,name')-> where("pid='".Request::post('pid')."'".$aw)->list_all_array();
	Response::json($array);
}
