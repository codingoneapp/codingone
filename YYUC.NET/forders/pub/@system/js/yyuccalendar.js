
var yyuccalendar = {
	css : '<style type="text/css">.yyuccalendar {position:absolute;width: 190px;border: solid 1px #BDBDBD;padding: 3px;top: -1000px;left:-1000px;z-index: 99;background-color: #ffffff;}.yyuccalendar .maindiv{width:188px;height:156px;border: solid 1px #C5D9E8;}.yyuccalendar .maindiv table{display:none;}.yyuccalendar a{color: #666666;cursor: pointer;}.yyuccalendar button{width:43px;text-align: center;background-color:#F0F0F0;color: #666666;cursor: pointer;border: solid 1px #CCCCCC;margin-left: 1px;margin-right: 0px;}.yyuccalendar .rqth{background-color:#BDEBEE;height: 23px;}.yyuccalendar .rqtd{background-color:#FBF9FF;height: 22px;}.yyuccalendar .rqth td{font-size: 12px;text-align: center;}.yyuccalendar .rqtd td{font-size: 12px;text-align: center;cursor: pointer;}.yyuccalendar .yftd,.yyuccalendar .nftd{background-color:#FBF9FF;height: 39px;}.yyuccalendar .yftd td,.yyuccalendar .nftd td{font-size: 13px;text-align: center;cursor: pointer;width: 62px;}</style>',
	html : '<div class="yyuccalendar"><table width="190px;" cellpadding="0" cellspacing="0" background="0"><tr><td width="20px;" align="right"><a id="yyuc_clendar_leftprev">☜</a></td><td width="150px;" align="center"><div style="height:23px;"><a id="yyuc_clendar_center"></a></div></td><td width="20px;" align="left"><a id="yyuc_clendar_rightnext">☞</a></td></tr><tr><td colspan="3"><div class="maindiv"><table id="yyuc_clendar_rq" width="100%" cellpadding="0" cellspacing="0" background="0"><tr class="rqth"><td>周</td><td>日</td><td>一</td><td>二</td><td>三</td><td>四</td><td>五</td><td>六</td></tr><tr class="rqtd"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr class="rqtd"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr class="rqtd"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr class="rqtd"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr class="rqtd"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr class="rqtd"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></table><table id="yyuc_clendar_yf" width="100%" cellpadding="0" cellspacing="0" background="0"><tr class="yftd"><td>一月</td><td>二月</td><td>三月</td></tr><tr class="yftd"><td>四月</td><td>五月</td><td>六月</td></tr><tr class="yftd"><td>七月</td><td>八月</td><td>九月</td></tr><tr class="yftd"><td>十月</td><td>十一月</td><td>十二月</td></tr></table><table id="yyuc_clendar_nf" width="100%" cellpadding="0" cellspacing="0" background="0"><tr class="nftd"><td></td><td></td><td></td></tr><tr class="nftd"><td></td><td></td><td></td></tr><tr class="nftd"><td></td><td></td><td></td></tr><tr class="nftd"><td></td><td></td><td></td></tr></table></div></td></tr><tr height="3px;"><td colspan="3"></td></tr><tr height="25px;" id="wt" style="display: none"><td colspan="3" style="font-size:14px;" align="center"><select class="tws"></select>时<select class="twf"></select>分<select class="twm"></select>秒</td></tr><tr><td colspan="3" align="left" nowrap="nowrap"><table cellpadding="0" cellspacing="0"><tr><td><button onclick="yyuccalendar.close()">关闭</button></td><td><button onclick="yyuccalendar.clear()">清除</button></td><td><button onclick="yyuccalendar.setnow()" class="yyuccnow">今天</button></td><td><button style="margin-left: 16px" onclick="yyuccalendar.confirm()">确定</button></td></tr></table></td></tr></table></div>',
	/**
	 * 返回这个日期的详细的参数信息
	 * 年 月 日 时 分 秒 星期
	 * @param date
	 */
	initCalendar : function (o,withTime,oth,isdatestr) {
		$(o).attr('readonly',true);
		$(o).unbind('keydown').keydown(function(e){
			if(e.keyCode==8){
				return false;
			}
		});
		$('.yyuccalendar').remove();
		yyuccalendar.o = o;
		yyuccalendar.isdatestr = isdatestr;
		var val = $.trim($(o).val());
		var date = null;
		if(val==''||val=='0'){
			date = new Date();
		}else{
			date = yyuccalendar.getDateFromStr(val);
		}
		//取得本月的第一天
		yyuccalendar.o.selectdate = new Date(date.getTime());
		$('body').append(yyuccalendar.html);		
		$('.yyuccalendar').css('left',$(o).offset().left);
		$('.yyuccalendar').css('top',$(o).offset().top+$(o).outerHeight());
		if(withTime){//如果需要选择时间
			yyuccalendar.wt = true;
			$('.yyuccalendar').find('.yyuccnow').text('现在');
			$('.yyuccalendar').find('#wt').show();
			for(var i=0;i<60;i++){
				var vv = i<10?'0'+i:i;
				var ht = '<option value="'+vv+'">'+vv+'</option>';
				if(i<24){
					$('.yyuccalendar').find('.tws').append(ht);
				}
				$('.yyuccalendar').find('.twf').append(ht);
				$('.yyuccalendar').find('.twm').append(ht);
			}
			$('.yyuccalendar').find('.tws').val(yyuccalendar.getbl(yyuccalendar.o.selectdate.getHours()));
			$('.yyuccalendar').find('.twf').val(yyuccalendar.getbl(yyuccalendar.o.selectdate.getMinutes()));
			$('.yyuccalendar').find('.twm').val(yyuccalendar.getbl(yyuccalendar.o.selectdate.getSeconds()));
		}else{
			yyuccalendar.wt = false;
		}
		if(oth){//如果含有隐藏元素
			yyuccalendar.oth = oth;		
		}
		$('.yyuccalendar').find('.maindiv').find('table').find('td').hover(function(){
			$(this).css('backgroundColor','#BDEBEE');
		},function(){
			if($(this).attr('now')!='now'){
				$(this).css('backgroundColor','');
			}
		});
		date.setDate(1);
		yyuccalendar.setrq(date);
		//点及其他地方关闭
		yyuccalendar.isoncal = false;
		$('.yyuccalendar').mousedown(function(){
			yyuccalendar.isoncal = true;
		});
		$('.yyuccalendar').mouseup(function(){
			yyuccalendar.isoncal = false;
		});
	},
	/**
	 * 批量年份框设置
	 */
	setnnf : function(year){
		//找到开始的年
		var ksn = year;
		while((ksn+72)%144!=0){
			ksn--;
		}		
		$('#yyuc_clendar_center').html(ksn+'-'+(ksn+143)).unbind();
		$('#yyuc_clendar_leftprev').unbind().click(function(){
			if(year-144<1800){
				yyuccalendar.setnnf(1800);
			}else{
				yyuccalendar.setnnf(year-144);
			}			
		});
		$('#yyuc_clendar_rightnext').unbind().click(function(){
			if(year+144>2519){
				yyuccalendar.setnnf(2519);
			}else{
				yyuccalendar.setnnf(year+144);
			}
		});
		$('.yyuccalendar').find('.maindiv').find('table').hide();
		$('#yyuc_clendar_nf').fadeIn('slow');
		$('#yyuc_clendar_nf').find('td').each(function(i){
			$(this).html(ksn+'-<br/>'+(ksn+11)+'&nbsp;');
			ksn+=12;
			$(this).unbind('click').click(function(){
				yyuccalendar.setnf(parseInt($.trim($(this).text().split('-')[0])));
			});
		});
	},
	/**
	 * 年份框设置
	 */
	setnf : function(year){
		//找到开始的年
		var ksn = year;
		while(ksn%12!=0){
			ksn--;
		}
		
		$('#yyuc_clendar_center').html(ksn+'-'+(ksn+11)).unbind().click(function(){
			yyuccalendar.setnnf(ksn);
		});
		$('#yyuc_clendar_leftprev').unbind().click(function(){
			if(year-12<1800){
				yyuccalendar.setnf(1800);
			}else{
				yyuccalendar.setnf(year-12);
			}			
		});
		$('#yyuc_clendar_rightnext').unbind().click(function(){
			if(year+12>2519){
				yyuccalendar.setnf(2519);
			}else{
				yyuccalendar.setnf(year+12);
			}
		});
		$('.yyuccalendar').find('.maindiv').find('table').hide();
		$('#yyuc_clendar_nf').fadeIn('slow');
		$('#yyuc_clendar_nf').find('td').each(function(i){
			$(this).html(ksn++);
			$(this).unbind('click').click(function(){
				yyuccalendar.setyf(parseInt($(this).text()));
			});
		});
	},
	/**
	 * 月份框设置
	 */
	setyf : function(year){
		$('#yyuc_clendar_center').html(year).unbind().click(function(){
			yyuccalendar.setnf(year);
		});
		$('#yyuc_clendar_leftprev').unbind().click(function(){
			if(year==1800){
				yyuccalendar.setyf(1800);
			}else{
				yyuccalendar.setyf(year-1);
			}			
		});
		$('#yyuc_clendar_rightnext').unbind().click(function(){
			if(year==2519){
				yyuccalendar.setyf(2519);
			}else{
				yyuccalendar.setyf(year+1);
			}
		});
		$('.yyuccalendar').find('.maindiv').find('table').hide();
		$('#yyuc_clendar_yf').fadeIn('slow');
		$('#yyuc_clendar_yf').find('td').each(function(i){
			var thedate = new Date(year,i,1);
			$(this).unbind('click').click(function(){
				yyuccalendar.setrq(thedate);
			});
		});
	},
	/**
	 * 日期框设置
	 * @param date
	 */
	setrq : function (date) {
		var jt = null;
		var jh = null;
		if(yyuccalendar.isSameMouth(date,new Date())){
			jt = (new Date()).getDate();
		}
		if(yyuccalendar.isSameMouth(date,yyuccalendar.o.selectdate)){
			jh = yyuccalendar.o.selectdate.getDate();
		}
		var rqms = yyuccalendar.getDateArray(date);
		var lastm = yyuccalendar.getPrevMouth(date);
		var nextm = yyuccalendar.getNextMouth(date);

		$('#yyuc_clendar_center').html(rqms[0]+'年'+rqms[1]+'月').unbind().click(function(){
			yyuccalendar.setyf(rqms[0]);
		});
		$('#yyuc_clendar_leftprev').unbind().click(function(){
			yyuccalendar.setrq(lastm[3]);
		});
		$('#yyuc_clendar_rightnext').unbind().click(function(){
			yyuccalendar.setrq(nextm[3]);
		});
		$('.yyuccalendar').find('.maindiv').find('table').hide();
		$('#yyuc_clendar_rq').fadeIn('slow');
		//填写日历
		var zs = rqms[7];//周数
		var qxo1 = rqms[6];//1号是星期几
		var byts = rqms[8];//本月天数
		var xrrq = 1;//要写入的日期
		$('#yyuc_clendar_rq').find('td').attr('now','').css('border','none').css('backgroundColor','')
		$('#yyuc_clendar_rq').find('.rqtd').each(function(i){
			var eq = 0;
			if(xrrq>byts&&nextm[0]>rqms[0]){
				$(this).find('td').eq(eq++).html('<b>1</b>');
			}else{
				$(this).find('td').eq(eq++).html('<b>'+zs+'</b>');
			}			
			if(i==0){
				var qmts = qxo1;//1号前面的天数
				for(var j=(lastm[2]-qmts+1);j<=lastm[2];j++){
					$(this).find('td').eq(eq).unbind('click').click(function(){
						yyuccalendar.setrq(lastm[3]);
					});
					$(this).find('td').eq(eq++).html('<font color="gray">'+j+'</font>');
				}
			}
			while(eq<8){
				var color = '';
				if(eq==1||eq==7){
					color = 'color="red"';
				}
				if(xrrq>byts){
					color = 'color="gray"';
					$(this).find('td').eq(eq).unbind('click').click(function(){
						yyuccalendar.setrq(nextm[3]);
					});
					$(this).find('td').eq(eq++).html('<font '+color+'>'+(xrrq-byts)+'</font>');
					xrrq++;
				}else{
					if(jt==xrrq){
						color+=' style="color:red;font-weight: bolder;"';
						$(this).find('td').eq(eq).css('backgroundColor','#BDEBEE').attr('now','now').attr('title','今天');
					}
					if(jh==xrrq){
						$(this).find('td').eq(eq).css('border','solid 1px #BDBDBD');
					}
					$(this).find('td').eq(eq).unbind('click').click(function(){
						$('#yyuc_clendar_rq').find('td').css('border','none');
						$(this).css('border','solid 1px #BDBDBD');
						yyuccalendar.o.selectdate = new Date(rqms[0],rqms[1]-1,parseInt($(this).text()));
					});
					$(this).find('td').eq(eq).unbind('dblclick').dblclick(function(){
						yyuccalendar.confirm();
					});
					$(this).find('td').eq(eq++).html('<font '+color+'>'+(xrrq++)+'</font>');
				}
				
			}
			zs++;
		});
	},
	/**
	 * 返回这个日期的详细的参数信息
	 * 年 月 日 时 分 秒 星期 一年的第几个星期 这个月的天数
	 * @param date
	 */
	getDateArray : function (date) {
		var res = [];
		res[0] = date.getFullYear();
		res[1] = date.getMonth() + 1;
		res[2] = date.getDate();
		res[3] = date.getHours();
		res[4] = date.getMinutes();
		res[5] = date.getSeconds();
		res[6] = date.getDay();
		res[7] = yyuccalendar.getWeekOfYear(date);
		res[8] = yyuccalendar.getMouthDays(res[0],res[1]);
		return res;
	},
	/**
	 * 获得补零形式
	 */
	getbl :function(num){
		return num<10?'0'+num:''+num;
	},
	/**
	 * 获得当前日期所在的周 是一年的第几周
	 * @param date
	 * @returns
	 */
	getWeekOfYear : function(date) {
		var firstWeekend = 7 - (new Date(date.getFullYear(),0,1)).getDay();
		var dayofyear = date.getDate();
		for ( var i = 0; i < date.getMonth(); i++) {
			dayofyear += yyuccalendar.getMouthDays(date.getFullYear(),i+1);
		}
		return parseInt((dayofyear - firstWeekend) / 7) + 1;
	},
	/**
	 * 根据字串取得日期
	 */
	getDateFromStr : function(str){
		if(str.indexOf(' ')!=-1){
			var dtts = str.split(' ');
			var dt = dtts[0];
			var tt = dtts[1];
			dts = dt.split('-');
			tts = tt.split(':');
			return new Date(parseInt(dts[0]),parseInt(dts[1])-1,parseInt(dts[2]),parseInt(tts[0]),parseInt(tts[1]),parseInt(tts[2]));
		}else{
			var dts = str.split('-');
			return new Date(parseInt(dts[0]),parseInt(dts[1])-1,parseInt(dts[2]));
		}
	},
	/**
	 * 获得上个月
	 */
	 getPrevMouth : function(date){
		var year = date.getFullYear();
		var month = date.getMonth() + 1;
		var res = [];
		if(month==1){
			res[0] = year - 1;
			res[1] = 12;
		}else{
			res[0] = year;
			res[1] = month - 1;			
		}
		res[2] = yyuccalendar.getMouthDays(res[0],res[1]);
		var thedate = new Date(date.getTime());
		thedate.setFullYear(res[0]);
		thedate.setMonth(res[1]-1);
		res[3] = thedate;
		return res;
	},
	/**
	 * 获得下个月
	 */
	 getNextMouth : function(date){
		var year = date.getFullYear();
		var month = date.getMonth() + 1;
		var res = [];
		if(month==12){
			res[0] = year + 1;
			res[1] = 1;	
		}else{
			res[0] = year;
			res[1] = month + 1;
		}
		res[2] = yyuccalendar.getMouthDays(res[0],res[1]);
		var thedate = new Date(date.getTime());
		thedate.setFullYear(res[0]);
		thedate.setMonth(res[1]-1);
		res[3] = thedate;
		return res;
	},
	/**
	 * 获得某年某月的天数
	 */
	 getMouthDays : function(year,month){
		month = month - 1;
		switch (month) {
		case 0:
		case 2:
		case 4:
		case 6:
		case 7:
		case 9:
		case 11:
			return 31;
			break;
		case 1:
			return ((year%4==0&&year%100!=0)?29:28);
			break;
		case 3:
		case 5:
		case 8:
		case 10:
			return 30;
			break;
		}
	},
	/**
	 * 是否同一天
	 */
	 isSameDay : function(date1,date2){
		return (date1.getFullYear()==date2.getFullYear()&&date1.getMonth()==date2.getMonth()&&date1.getDate()==date2.getDate());
	},
	/**
	 * 是否同一月
	 */
	 isSameMouth : function(date1,date2){
		return (date1.getFullYear()==date2.getFullYear()&&date1.getMonth()==date2.getMonth());
	},
	/**
	 * 确定回填
	 */
	 confirm : function(){
		var str = yyuccalendar.o.selectdate.getFullYear()+'-'+yyuccalendar.getbl(yyuccalendar.o.selectdate.getMonth() + 1)+'-'+yyuccalendar.getbl(yyuccalendar.o.selectdate.getDate());
		if(yyuccalendar.wt){
			str+=(' '+$('.yyuccalendar').find('.tws').val()+':'+$('.yyuccalendar').find('.twf').val()+':'+$('.yyuccalendar').find('.twm').val());
			yyuccalendar.o.selectdate.setHours(parseInt($('.yyuccalendar').find('.tws').val()),parseInt($('.yyuccalendar').find('.twf').val()),parseInt($('.yyuccalendar').find('.twm').val()));
		}
		$(yyuccalendar.o).val(str);
		if(yyuccalendar.oth && !yyuccalendar.isdatestr){
			$(yyuccalendar.oth).val(yyuccalendar.o.selectdate.getTime()/1000);
		}else if(yyuccalendar.oth){
			$(yyuccalendar.oth).val(str);
		}
		yyuccalendar.close();
	},
	close : function(){
		$('.yyuccalendar').remove();
	},
	setnow : function(){
		yyuccalendar.o.selectdate = new Date();
		if(yyuccalendar.wt){
			$('.yyuccalendar').find('.tws').val(yyuccalendar.getbl(yyuccalendar.o.selectdate.getHours()));
			$('.yyuccalendar').find('.twf').val(yyuccalendar.getbl(yyuccalendar.o.selectdate.getMinutes()));
			$('.yyuccalendar').find('.twm').val(yyuccalendar.getbl(yyuccalendar.o.selectdate.getSeconds()));
		}
		yyuccalendar.confirm();
	},
	clear : function(){
		$(yyuccalendar.o).val('');
		if(yyuccalendar.oth){
			$(yyuccalendar.oth).val('');
		}
	}
}
$(document).ready(function(){
	$('head').append(yyuccalendar.css);
	$(document).mousedown(function(){
		if($('.yyuccalendar').size()>0 && !yyuccalendar.isoncal){
			yyuccalendar.close();
		}
	});
});