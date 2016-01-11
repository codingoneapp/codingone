<?php
class Vercode{
	public static function outputimg($num = 4, $ischinese = false,$image_x = null,$image_y=null ){
		$res = '';
		$ch_str = 'ASDFGHJKLZXCVBNMQWERTYUPqwertyuipasdfghjkzxcvbnm23456789';
		if($ischinese){
			$ch_str="三于干亏士工土才寸下大丈与万上小口巾山千乞川亿个勺久凡及夕丸么广亡门义之尸弓己已子卫也女飞刃习叉马乡丰王井开夫天无元专云扎艺木五支厅不太犬区历尤友匹车巨牙屯比互切瓦止少日中冈贝内水见午牛手毛气升长仁什片仆化仇币仍仅斤爪反介父从今凶分乏公仓月氏勿欠风丹匀乌凤勾文六方火为斗忆订计户认心尺引丑巴孔队办以允予劝双书幻玉刊示末未击打巧正扑扒功扔去甘世古节本术可丙左厉右石布龙平灭轧东卡北占业旧帅归且旦目叶甲申叮电号田由史只央兄叼叫另叨叹四生失禾丘付仗代仙们仪白仔他斥瓜乎丛令用甩印乐句匆册犯外处冬鸟务包饥主市立闪兰半汁汇头汉宁穴它讨写让礼训必议讯记永司尼民出辽奶奴加召皮边发孕圣对台矛纠母幼丝式刑动扛寺吉扣考托老执巩圾扩扫地扬场耳共芒亚芝朽朴机权过臣再协西压厌在有百存而页匠夸夺灰达列死成夹轨邪划迈毕至此贞师尘尖劣光当早吐吓虫曲团同吊吃因吸吗屿帆岁回岂刚则肉网年朱先丢舌竹迁乔伟传乒乓休伍伏优伐延件任伤价份华仰仿伙伪自血向似后行舟全会杀合兆企众爷伞创肌朵杂危旬旨负各名多争色壮冲冰庄庆亦刘齐交次衣产决充妄闭问闯羊并关米灯州汗污江池汤忙兴宇守宅字安讲军许论农讽设访寻那迅尽导异孙阵阳收阶阴防如妇好她妈戏羽观欢买红级约纪驰巡";
		}
		$slen = mb_strlen($ch_str) - 1;
		if($image_x === null){
			$image_x = 30*$num;
		}
		if($image_y === null){
			$image_y = 45;
		}
		$im = imagecreate($image_x,$image_y);
		//这里取图片底色为白色
		$bkg = ImageColorAllocate($im,255,255,255);
		//显示的字体样式,这个要把文件放到对应的目录中,如果你没有文件就去window的字体文件中找一个吧。
		$fnt = YYUC_LIB.'plugin/font/fz.ttf';
		//为图像分配一些颜色
		$white = ImageColorAllocate($im,234,185,95);
		//在图片上画椭圆弧,指定下坐标点
		imagearc($im, 150, 8, 20, 20, 75, 170, $white);
		imagearc($im, 180, 7,50, 30, 75, 175, $white);
		//在图片上画一条线段,指定下坐标点
		imageline($im,20,20,180,30,$white);
		imageline($im,20,18,170,50,$white);
		imageline($im,25,50,80,50,$white);
		//乱点的数量
		$noise_num=intval($image_x/4);
		$line_num= intval($image_y/16);
		for($i=0;$i<$noise_num;$i++){
			$noise_color=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
			//在一个坐标点上画一个单一像素,这个点上面定义了，是黑色的。
			imagesetpixel($im,mt_rand(0,$image_x),mt_rand(0,$image_y),$noise_color);
		}


		for($i=0;$i<$line_num;$i++){
			$line_color=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
			//在两个坐标点间画一条线，颜色在上面定义
			imageline($im,mt_rand(0,$image_x),mt_rand(0,$image_y),mt_rand(0,$image_x),mt_rand(0,$image_y),$line_color);
		}
		
		for($i=0;$i<$num+1;$i++){
			$fcolor = imagecolorallocate($im,mt_rand(50,150),mt_rand(50,150),mt_rand(50,150));
			$str = mb_substr($ch_str, mt_rand(0,$slen), 1);
			if($ischinese){
				$xx = ($image_x/$num)*$i+mt_rand(2,6);
				$yy = ($image_y/2)+mt_rand(5,10);
				$fontsize =  mt_rand(15,19);
			}else{
				$xx = ($image_x/$num)*$i+mt_rand(3,8);
				$yy = ($image_y/2)+mt_rand(5,15);
				$fontsize =  mt_rand(18,26);
			}			
			$fontangle = mt_rand(-30,30);
			if($i == $num){
				imagettftext($im, $fontsize, $fontangle, $xx,	$yy, $fcolor,	$fnt, '.');
			}else{
				$res .= $str;
				imagettftext($im, $fontsize, $fontangle, $xx+1,	$yy, $fcolor,	$fnt, $str);
			}
		}
		Session::set('YYUC_vercode',$res);
		imagettftext($im, $fontsize, $fontangle, $xx+1,	$yy, $fcolor,	$fnt, $str);
		Response::mime(Mime::$gif);
		imagegif($im);
		imagedestroy($im);
		return $res;
	}
}