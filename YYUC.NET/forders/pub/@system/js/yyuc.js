window.YYUC = {};

(function(jQuery,_){
	//md5操作
	var hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
	var b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
	var chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */

	/*
	 * These are the functions you'll usually want to call
	 * They take string arguments and return either hex or base-64 encoded strings
	 */
	function hex_md5(s){ return binl2hex(core_md5(str2binl(s), s.length * chrsz));}
	function b64_md5(s){ return binl2b64(core_md5(str2binl(s), s.length * chrsz));}
	function str_md5(s){ return binl2str(core_md5(str2binl(s), s.length * chrsz));}
	function hex_hmac_md5(key, data) { return binl2hex(core_hmac_md5(key, data)); }
	function b64_hmac_md5(key, data) { return binl2b64(core_hmac_md5(key, data)); }
	function str_hmac_md5(key, data) { return binl2str(core_hmac_md5(key, data)); }

	/*
	 * Perform a simple self-test to see if the VM is working
	 */
	function md5_vm_test()
	{
	  return hex_md5("abc") == "900150983cd24fb0d6963f7d28e17f72";
	}

	/*
	 * Calculate the MD5 of an array of little-endian words, and a bit length
	 */
	function core_md5(x, len)
	{
	  /* append padding */
	  x[len >> 5] |= 0x80 << ((len) % 32);
	  x[(((len + 64) >>> 9) << 4) + 14] = len;

	  var a =  1732584193;
	  var b = -271733879;
	  var c = -1732584194;
	  var d =  271733878;

	  for(var i = 0; i < x.length; i += 16)
	  {
	    var olda = a;
	    var oldb = b;
	    var oldc = c;
	    var oldd = d;

	    a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
	    d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
	    c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
	    b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
	    a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
	    d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
	    c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
	    b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
	    a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
	    d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
	    c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
	    b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
	    a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
	    d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
	    c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
	    b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);

	    a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
	    d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
	    c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
	    b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
	    a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
	    d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
	    c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
	    b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
	    a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
	    d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
	    c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
	    b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
	    a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
	    d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
	    c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
	    b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);

	    a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
	    d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
	    c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
	    b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
	    a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
	    d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
	    c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
	    b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
	    a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
	    d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
	    c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
	    b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
	    a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
	    d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
	    c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
	    b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);

	    a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
	    d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
	    c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
	    b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
	    a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
	    d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
	    c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
	    b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
	    a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
	    d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
	    c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
	    b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
	    a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
	    d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
	    c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
	    b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);

	    a = safe_add(a, olda);
	    b = safe_add(b, oldb);
	    c = safe_add(c, oldc);
	    d = safe_add(d, oldd);
	  }
	  return Array(a, b, c, d);

	}

	/*
	 * These functions implement the four basic operations the algorithm uses.
	 */
	function md5_cmn(q, a, b, x, s, t)
	{
	  return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
	}
	function md5_ff(a, b, c, d, x, s, t)
	{
	  return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
	}
	function md5_gg(a, b, c, d, x, s, t)
	{
	  return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
	}
	function md5_hh(a, b, c, d, x, s, t)
	{
	  return md5_cmn(b ^ c ^ d, a, b, x, s, t);
	}
	function md5_ii(a, b, c, d, x, s, t)
	{
	  return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
	}

	/*
	 * Calculate the HMAC-MD5, of a key and some data
	 */
	function core_hmac_md5(key, data)
	{
	  var bkey = str2binl(key);
	  if(bkey.length > 16) bkey = core_md5(bkey, key.length * chrsz);

	  var ipad = Array(16), opad = Array(16);
	  for(var i = 0; i < 16; i++)
	  {
	    ipad[i] = bkey[i] ^ 0x36363636;
	    opad[i] = bkey[i] ^ 0x5C5C5C5C;
	  }

	  var hash = core_md5(ipad.concat(str2binl(data)), 512 + data.length * chrsz);
	  return core_md5(opad.concat(hash), 512 + 128);
	}

	/*
	 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
	 * to work around bugs in some JS interpreters.
	 */
	function safe_add(x, y)
	{
	  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
	  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
	  return (msw << 16) | (lsw & 0xFFFF);
	}

	/*
	 * Bitwise rotate a 32-bit number to the left.
	 */
	function bit_rol(num, cnt)
	{
	  return (num << cnt) | (num >>> (32 - cnt));
	}

	/*
	 * Convert a string to an array of little-endian words
	 * If chrsz is ASCII, characters >255 have their hi-byte silently ignored.
	 */
	function str2binl(str)
	{
	  var bin = Array();
	  var mask = (1 << chrsz) - 1;
	  for(var i = 0; i < str.length * chrsz; i += chrsz)
	    bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
	  return bin;
	}

	/*
	 * Convert an array of little-endian words to a string
	 */
	function binl2str(bin)
	{
	  var str = "";
	  var mask = (1 << chrsz) - 1;
	  for(var i = 0; i < bin.length * 32; i += chrsz)
	    str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
	  return str;
	}

	/*
	 * Convert an array of little-endian words to a hex string.
	 */
	function binl2hex(binarray)
	{
	  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
	  var str = "";
	  for(var i = 0; i < binarray.length * 4; i++)
	  {
	    str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
	           hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
	  }
	  return str;
	}

	/*
	 * Convert an array of little-endian words to a base-64 string
	 */
	function binl2b64(binarray)
	{
	  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
	  var str = "";
	  for(var i = 0; i < binarray.length * 4; i += 3)
	  {
	    var triplet = (((binarray[i   >> 2] >> 8 * ( i   %4)) & 0xFF) << 16)
	                | (((binarray[i+1 >> 2] >> 8 * ((i+1)%4)) & 0xFF) << 8 )
	                |  ((binarray[i+2 >> 2] >> 8 * ((i+2)%4)) & 0xFF);
	    for(var j = 0; j < 4; j++)
	    {
	      if(i * 8 + j * 6 > binarray.length * 32) str += b64pad;
	      else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
	    }
	  }
	  return str;
	}
	
	_.md5 = function(code){
		  return hex_md5(code);
	}
	
	//base64操作
	function Base64() {  
		   
	    // private property  
	    _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";  
	   
	    // public method for encoding  
	    this.encode = function (input) {  
	        var output = "";  
	        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;  
	        var i = 0;  
	        input = _utf8_encode(input);  
	        while (i < input.length) {  
	            chr1 = input.charCodeAt(i++);  
	            chr2 = input.charCodeAt(i++);  
	            chr3 = input.charCodeAt(i++);  
	            enc1 = chr1 >> 2;  
	            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);  
	            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);  
	            enc4 = chr3 & 63;  
	            if (isNaN(chr2)) {  
	                enc3 = enc4 = 64;  
	            } else if (isNaN(chr3)) {  
	                enc4 = 64;  
	            }  
	            output = output +  
	            _keyStr.charAt(enc1) + _keyStr.charAt(enc2) +  
	            _keyStr.charAt(enc3) + _keyStr.charAt(enc4);  
	        }  
	        return output;  
	    }  
	   
	    // public method for decoding  
	    this.decode = function (input) {  
	        var output = "";  
	        var chr1, chr2, chr3;  
	        var enc1, enc2, enc3, enc4;  
	        var i = 0;  
	        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");  
	        while (i < input.length) {  
	            enc1 = _keyStr.indexOf(input.charAt(i++));  
	            enc2 = _keyStr.indexOf(input.charAt(i++));  
	            enc3 = _keyStr.indexOf(input.charAt(i++));  
	            enc4 = _keyStr.indexOf(input.charAt(i++));  
	            chr1 = (enc1 << 2) | (enc2 >> 4);  
	            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);  
	            chr3 = ((enc3 & 3) << 6) | enc4;  
	            output = output + String.fromCharCode(chr1);  
	            if (enc3 != 64) {  
	                output = output + String.fromCharCode(chr2);  
	            }  
	            if (enc4 != 64) {  
	                output = output + String.fromCharCode(chr3);  
	            }  
	        }  
	        output = _utf8_decode(output);  
	        return output;  
	    }  
	   
	    // private method for UTF-8 encoding  
	    _utf8_encode = function (string) {  
	        string = string.replace(/\r\n/g,"\n");  
	        var utftext = "";  
	        for (var n = 0; n < string.length; n++) {  
	            var c = string.charCodeAt(n);  
	            if (c < 128) {  
	                utftext += String.fromCharCode(c);  
	            } else if((c > 127) && (c < 2048)) {  
	                utftext += String.fromCharCode((c >> 6) | 192);  
	                utftext += String.fromCharCode((c & 63) | 128);  
	            } else {  
	                utftext += String.fromCharCode((c >> 12) | 224);  
	                utftext += String.fromCharCode(((c >> 6) & 63) | 128);  
	                utftext += String.fromCharCode((c & 63) | 128);  
	            }  
	   
	        }  
	        return utftext;  
	    }  
	   
	    // private method for UTF-8 decoding  
	    _utf8_decode = function (utftext) {  
	        var string = "";  
	        var i = 0;  
	        var c = c1 = c2 = 0;  
	        while ( i < utftext.length ) {  
	            c = utftext.charCodeAt(i);  
	            if (c < 128) {  
	                string += String.fromCharCode(c);  
	                i++;  
	            } else if((c > 191) && (c < 224)) {  
	                c2 = utftext.charCodeAt(i+1);  
	                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));  
	                i += 2;  
	            } else {  
	                c2 = utftext.charCodeAt(i+1);  
	                c3 = utftext.charCodeAt(i+2);  
	                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));  
	                i += 3;  
	            }  
	        }  
	        return string;  
	    }  
	}	
	_.base64decode = function(code){
		var b = new Base64();
		return b.decode(code);
	}
	_.base64encode = function(code){
		var b = new Base64();
		return b.encode(code);
	}
	//禁用额外动作
	document.ondragstart = function(){return false;};
	
	_.ready  = function(fun){
		if(!window.yyuc_initfuns){
			window.yyuc_initfuns = [];
		}
		window.yyuc_initfuns[window.yyuc_initfuns.length] = fun;
	}
	
	////////////
	_.nl2br = function(str, is_xhtml) {
		var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>';
		return (str + '').replace(/ /g, '&nbsp').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
	};
	///////////
	_.uuid = function(){
		return 'xxxxxxxxxxxxxxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
		      var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
		      return v.toString(16);
		});
	};
	_.browser = {};
	_.browser.mozilla = /firefox/.test(navigator.userAgent.toLowerCase());
	_.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
	_.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
	if(!!window.ActiveXObject){
		_.browser.ie6 = _.browser.ie = _.browser.msie = true;
	}else if(!jQuery.support.leadingWhitespace){
		if(document.documentMode){
			_.browser.ie8 = true;
		}else{
			_.browser.ie7 = true;
		}
		_.browser.ie = _.browser.msie = true;
	}else if(/chrome/.test(navigator.userAgent.toLowerCase())){
		_.browser.chrome = true;
	}else if(/safari/.test(navigator.userAgent.toLowerCase())){
		_.browser.safari = true;
	};
	_.getStyle = window.getStyle = jQuery.getStyle = function(url){
		var styleTag = document.createElement("link");
		styleTag.setAttribute('type', 'text/css');
		styleTag.setAttribute('rel', 'stylesheet');
		styleTag.setAttribute('href', url);
		jQuery("head")[0].appendChild(styleTag);
	};
	jQuery(document).on('mousemove dragmove',function(ev){
		if(ev.pageX || ev.pageY){
			_.mleft = jQuery.mleft = ev.pageX;
			_.mtop = jQuery.mtop = ev.pageY;
		}else{
			_.mleft = jQuery.mleft = ev.clientX+jQuery(document).scrollLeft();
			_.mtop = jQuery.mtop = ev.clientY+jQuery(document).scrollTop();
		}
		if(_.yyuc_simpledrag){
			_.yyuc_simpledrago.css('left',_.yyuc_simpledragl+_.mleft-_.yyuc_simpledragx);
			_.yyuc_simpledrago.css('top',_.yyuc_simpledragt+_.mtop-_.yyuc_simpledragy);
		}
	});
	jQuery(document).on('mouseup',function(){
		if(_.yyuc_simpledrag){
			if(_.yyuc_simpledragb[0].setCapture){
				_.yyuc_simpledragb[0].releaseCapture();
			}
			_.yyuc_simpledrag = false;
		}
		
	});
	
	//简单拖动 其实有些时候只需要简单拖动
	_.drag = function(jo,jbar){
		if(!jbar){
			jbar = jo;
		}
		jbar.on('mousedown dragstart',function(){
			_.yyuc_simpledragx = jQuery.mleft;
			_.yyuc_simpledragy = jQuery.mtop;
			_.yyuc_simpledrago = jo;		
			_.yyuc_simpledragb = jbar;
			_.yyuc_simpledragl = parseInt(jo.css('left').replace('px',''));		
			_.yyuc_simpledragt = parseInt(jo.css('top').replace('px',''));
			_.yyuc_simpledrag = true;
			if(_.yyuc_simpledragb[0].setCapture){
				_.yyuc_simpledragb[0].setCapture();
			}
		});
	};
	
	
	_.loadyyucobj = function(justload){
		var inphidden = jQuery('[rel="yyuc"]');
		if(inphidden.size()>0){
			inphidden = inphidden.eq(0);
			inphidden.attr('rel','yyucok');
			var obj =  inphidden.attr('relobj');
			if(eval('"undefined" == typeof '+obj)){
				if(obj=='yyuccalendar'){
					jQuery.getScript(yyuc_jspath+'datePicker/WdatePicker.js',function(){						
						_.loadyyucobj();
					});
				}else if(obj=='elfinder'){
					_.loadelfinder(inphidden);
					_.loadyyucobj();
				}else if(obj=='kindeditor'){
					jQuery.getScript(yyuc_jspath+'kindeditor/kindeditor-min.js',function(){
						KindEditor.basePath = yyuc_jspath+'kindeditor/';
						_.loadkindeditor(inphidden);
						_.loadyyucobj();
					});
				}else if(obj=='yyuccolor'){
					_.getStyle(yyuc_jspath+'jqcolor/jpicker.css');
					jQuery.getScript(yyuc_jspath+'jqcolor/jpicker.js',function(){
						jQuery.fn.jPicker.defaults.images.clientPath=yyuc_jspath+'jqcolor/images/';					
						_.loadjqcolor(inphidden);
						_.loadyyucobj();						
					});
				}else if(obj=='yyucmcalendar'){
					getStyle(yyuc_jspath+'mobileDate/css/mobiscroll.custom-2.5.2.min.css');
					jQuery.getScript(yyuc_jspath+'mobileDate/js/mobiscroll.custom-2.5.2.min.js',function(){						
						_.loadmdate(inphidden);
						_.loadyyucobj();
					});
				}else {
					jQuery.getScript(yyuc_jspath+'js/'+obj+'.js',function(){				
						_.loadyyucobj();					
					});
				}
			}else{
				if(obj=='kindeditor'){
					_.loadkindeditor(inphidden);
				}else if(obj=='yyuccolor'){
					_.loadjqcolor(inphidden);
				}else if(obj=='yyucmcalendar'){
					_.loadmdate(inphidden);
				}else if(obj=='elfinder'){
					_.loadelfinder(inphidden);
				}
				_.loadyyucobj();
			}		
		}else if(!justload){
			//执行加载万JS之后的动作
			if(window.yyucselectinput){
				jQuery('select[relobj="yyucselectinput"]').searchable();
			}			
			
			if(window.yyuc_initfuns){
				for(var yyuc_i=0;yyuc_i<window.yyuc_initfuns.length;yyuc_i++){
					setTimeout(window.yyuc_initfuns[yyuc_i],0);
				}
			}
		}
	};
	
	_.loadelfinder = function(jo,force_rebind){
		if(jo.data('haselfinder') && !force_rebind){
			return;
		}
		jo.data('haselfinder',true);
		jo.click(function(){
			window.YYUC_xfilebuttonid = this.id;
			window.yyuc_xfilemultiple = !!eval(jQuery(this).attr('relmultiple'));
			window.yyuc_xfiles = function(m,n){						
				jQuery('img[relobj="elfinder_'+window.YYUC_xfilebuttonid+'"]').attr('src',m);
				jQuery('input[relobj="elfinder_'+window.YYUC_xfilebuttonid+'"]').val(m);
				window.yyuc_xfiles = null;
			}
			if(jQuery.trim(jQuery(this).attr('relfun'))!=''){
				var cbk = jQuery(this).attr('relfun');
				eval('window.yyuc_xfiles = '+cbk+';');
			}			
			_.pophtml('<iframe src="/@system/upload/" style="width:930px;height:530px;border:none;background-color: #dfdfdf;" ></iframe>',930,530,true);				  
		});
	};
	
	
	_.loadmdate = function(jo){
		var currYear = (new Date()).getFullYear();	
		var opt={};
		opt.date = {preset : 'date'};
		opt.datetime = {preset : 'datetime'};
		opt.time = {preset : 'time'};
		opt.defaults = {
			theme: 'android-ics light', //皮肤样式
	        display: 'modal', //显示方式 
	        mode: 'scroller', //日期选择模式
			lang:'zh',
	        startYear:currYear - 80, //开始年份
	        endYear:currYear + 10 //结束年份
		};
		var optDateTime = jQuery.extend(opt['datetime'], opt['defaults']);
		if(jo.attr('mdate')=='date'){
			optDateTime = jQuery.extend(opt['date'], opt['defaults']);
			jo.scroller(optDateTime);
		}else{
			jo.mobiscroll(optDateTime).datetime(optDateTime);
		}
		
	}
	
	_.loadjqcolor  = function(jo){
		var initcolor = jo.val();
		if(jQuery.trim(jo.val())==''){
			initcolor = 'ffffffff';
		}
		jQuery('#'+jo.attr('colorid')).jPicker({window:{alphaSupport:eval(jo.attr('needal')),title:'颜色选择',expandable:true,position:{x:'screenCenter',y:'screenCenter'}}, color:
	    {active: new jQuery.jPicker.Color({ahex:initcolor})}},function(color){
	  	  var all = color.val('all');
		  if(all&&all.ahex){
			  jo.val(all.ahex);
		  }
	    });
	}
	
	_.loadkindeditor = function(jo){
		if(!window.kindeditorAfterChange){
			window.kindeditorAfterChange = function(){
				$(window).trigger('kindeditorchange');
			};
		}
		eval('window.kindeditor_'+jo.attr('editorid')+' = KindEditor.create("#'+jo.attr('editorid')+'",'+jo.val()+');');
		jQuery('form').submit(function(){
			jQuery('#'+jo.attr('editorid')).val(eval('window.kindeditor_'+jo.attr('editorid')+'.html()'));
		});
	}
	
	_.evalJSON = _.eval_json = function(strJson) {
		if(window.JSON && JSON.parse){
			return JSON.parse(strJson);
		}
		return eval("(" + strJson + ")");
	};
	
	//JSON
	_.to_json = _.toJSON = function(object) {
		if(window.JSON && JSON.stringify){
			return JSON.stringify(object);
		}
		var type = typeof object;
		if(!object){
			type = 'undefined'
		}
		if ('object' == type) {
			if (Array == object.constructor)
				type = 'array';
			else if (RegExp == object.constructor)
				type = 'regexp';
			else
				type = 'object';
		}
		switch (type) {
		case 'undefined':
		case 'unknown':
			return;
			break;
		case 'function':
		case 'boolean':
		case 'regexp':
			return object.toString();
			break;
		case 'number':
			return isFinite(object) ? object.toString() : 'null';
			break;
		case 'string':
			return '"' + object.replace(/(\\|\")/g, "\\\$1").replace(/\n|\r|\t/g,function(){
								var a = arguments[0];
								return (a == '\n') ? '\\n' : (a == '\r') ? '\\r' : (a == '\t') ? '\\t' : "";
							}) + '"';
			break;
		case 'object':
			if (object === null)
				return 'null';
			var results = [];
			for ( var property in object) {
				var value = _.to_json(object[property]);
				if (value != undefined)
					results.push(_.to_json(property) + ':' + value);
			}
			return '{' + results.join(',') + '}';
			break;
		case 'array':
			var results = [];
			for ( var i = 0; i < object.length; i++) {
				var value = _.to_json(object[i]);
				if (value !== undefined){
					results.push(value);					
				}else{
					results.push('""');
				}
			}
			return '[' + results.join(',') + ']';
			break;
		}
	};
	
	//分页页面直接跳转
	_.page = function(fquery){
		var page = parseInt(jQuery('#YYUC_PAGE_jumptxt').val());
		if(!page){
			page = 1;
		}
		jQuery(fquery).append('<input type="hidden" name="YYUC_PAGE_jumptxt" value="'+page+'">').submit();
	}
	
	//url格式解析
	_.parse_url = function (str, component) {
		var o = {
			strictMode : false,
			key : [ "source", "protocol", "authority", "userInfo", "user", "password", "host", "port", "relative", "path", "directory", "file", "query", "anchor" ],

			q : {name : "queryKey",	parser : /(?:^|&)([^&=]*)=?([^&]*)/g},

			parser : {
				strict : /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
				loose : /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/\/?)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
			}
		};

		var m = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
		uri = {},
		i = 14;
		while (i--) {
			uri[o.key[i]] = m[i] || "";
		}

		if (uri.path != '') {
			uri.file = uri.path.replace(/^.*[\/\\]/g, '');
		}

		var retArr = {};
		if (uri.protocol !== '') {
			retArr.scheme = uri.protocol;
		}

		if (uri.host !== '') {
			retArr.host = uri.host;
		}

		if (uri.port !== '') {
			retArr.port = uri.port;
		}

		if (uri.user !== '') {
			retArr.user = uri.user;
		}

		if (uri.password !== '') {
			retArr.pass = uri.password;
		}

		if (uri.path !== '') {
			retArr.path = uri.path;
		}

		if (uri.file) {
			retArr.file = uri.file;
		}

		if (uri.query !== '') {
			retArr.query = uri.query;
		}

		if (uri.anchor !== '') {
			retArr.fragment = uri.anchor;
		}

		return retArr;
	};
	
	_.deal_url = function(baseUrl,myUrl){
		myUrl = jQuery.trim(myUrl);
		if(myUrl.indexOf('http')===0){
			return myUrl;
		}
		var urlbz = _.parse_url(jQuery.trim(baseUrl));
		if(myUrl.indexOf('/')===0){
			return urlbz['scheme']+'://'+urlbz['host']+myUrl;
		}else {
			if(myUrl.indexOf('./')===0){
				myUrl = myUrl.substr(2);
			}
			if(jQuery.trim(urlbz['path'])!=''){
				var subpath = urlbz['path'];
				if(subpath.substr(subpath.length-1)!='/'){
					var ind = subpath.lastIndexOf('/');
					subpath = subpath.substr(0,ind+1);
				}
				while(myUrl.indexOf('../')===0){
					subpath = subpath.substr(0,subpath.length-1);
					myUrl = myUrl.substr(3);
					var ind = subpath.lastIndexOf('/');
					subpath = subpath.substr(0,ind+1);
				}
				// 最后一位是：/
				return urlbz['scheme']+'://'+urlbz['host']+subpath+myUrl;	
			}else{
				return urlbz['scheme']+'://'+urlbz['host']+'/'+myUrl;
			}
		}
	};

	//ajax请求
	_.ajax = function(url,jsobject,successfun,errorfun,pobj,cache){
		if(!pobj){
			pobj = window;
		}
		if(!cache){
			cache = false;
		}
		var ttype = jsobject?'post':'get';
		var md5key = '';
		if(cache){
			md5key = _.md5(url+_.to_json(jsobject));
			if(window[md5key]){
				if(typeof successfun =='string'){
					eval(successfun);
				}else{
					successfun.apply(pobj,_.evalJSON(window[md5key]));
				}
				return;
			}
		}
		var async = true;
		if(errorfun===false){
			async = false;
		}
		return jQuery.ajax({
			url: url,
			type: ttype,
			data:jsobject,
			async:async,
			cache: cache,
			//dataType:"json",
			success: function(msg,reqdata){
				if(cache){
					window[md5key] = _.to_json([msg,reqdata]);
				}
				if(successfun){
					if(typeof successfun =='string'){
						eval(successfun);
					}else{
						successfun.apply(pobj,[msg,reqdata]);
					}				
				}
			},error : function(obj,errmsg){
				if(errorfun){
					errorfun.apply(pobj,[errmsg]);
				}
			}
		});
	};
	
	//jsobject为json对象
	_.ajaxjson = function(url,jsobject,successfun,errorfun,pobj){
		var jsondata = _.to_json(jsobject);
		var req = {data:jsondata};
		return _.ajax(url,req,successfun,errorfun,pobj);
	}
	
	//修正了jquery的ajax缓存机制的异步请求
	_.ajaxcache = function(url,jsobject,successfun,errorfun,pobj){
		return _.ajax(url,jsobject,successfun,errorfun,pobj,true);
	}
	
	//
	_.goto = function(url,isblank){
		_.cookie('YYUCLASTPAGE',location.href);
		if(isblank){
			isblank = 'target="_blank"';
		}else{
			isblank = '';
		}
		var link = jQuery('<form '+isblank+'><input type="hidden" name="@YY@UC@" value="GO"></form>');
		link.attr('action',url);
		jQuery('body').append(link);
		setTimeout(function(){link.submit();},0);
	};
	
	function yyuc_cookie_encode(string){
		var decoded = encodeURIComponent(string);
		var ns = decoded.replace(/(%7B|%7D|%3A|%22|%23|%5B|%5D)/g,function(charater){return decodeURIComponent(charater);});
		return ns;
	}
	
	// cookie插件
	_.cookie = function (key, value, options) {
	    if (arguments.length > 1 && String(value) !== "[object Object]") {
	        options = jQuery.extend({path:'/',expires:(new Date((new Date()).getTime()+1000*60*60*24))}, options);
	        
	        if (value === null || value === undefined) {
	            options.expires = -1;
	        }

	        if (typeof options.expires === 'number') {
	            var days = options.expires, t = options.expires = new Date();
	            t.setDate(t.getDate() + days);
	        }

	        value = String(value);
	        
	        return (document.cookie = [
	            encodeURIComponent(key), '=',
	            options.raw ? value : yyuc_cookie_encode(value),
	            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
	            options.path ? '; path=' + options.path : '',
	            options.domain ? '; domain=' + options.domain : '',
	            options.secure ? '; secure' : ''
	        ].join(''));
	    }

	    // key and possibly options given, get cookie...
	    options = value || {};
	    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
	    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
	};
	
	_.toast = function(txt,fun){
		$('#yyuc_toast_temp_div').remove();
		if(txt===false){
			return;
		}
		jQuery('.tusi').remove();
		var div = jQuery('<div id="yyuc_toast_temp_div" style="background: url('+yyuc_jspath+'/img/loadb.png);border-radius:10px;max-width: 85%;min-height: 77px;min-width: 270px;position: absolute;left: -1000px;top: -1000px;text-align: center;border-radius:10px;"><span style="color: #ffffff;line-height: 77px;font-size: 23px;">'+txt+'</span></div>');
		jQuery('body').append(div);
		div.css('zIndex',29891014);
		div.css('left',parseInt((jQuery(window).width()-div.width())/2));
		var top = parseInt(jQuery(window).scrollTop()+(jQuery(window).height()-div.height())/2);
		div.css('top',top);
		setTimeout(function(){
			div.animate({ 
		        top: top-200,
		        opacity:0}, {
		        duration:888,
		        complete:function(){
		        	div.remove();
		        	if(fun){
		        		fun();
		        	}
		        }
		    });
		},1888);
	};
	
	_.loading = function(txt){
		if(txt === false){
			jQuery('.qp_lodediv,.qp_maskdiv').remove();
		}else{
			jQuery('.qp_lodediv,.qp_maskdiv').remove();
			$('body').append('<div class="qp_maskdiv" style="z-index:29891013;position: fixed;left:0px;top:0px;background:rgba(0,0,0,0.1)"></div>');
			$('.qp_maskdiv').width($(window).width()).height($(window).height());
			
			var div = jQuery('<div class="qp_lodediv" style="background: url('+yyuc_jspath+'/img/loadb.png);border-radius:10px;min-width: 269px;height: 90px;position: absolute;left: -1000px;top: -1000px;text-align: center;"><span style="color: #ffffff;line-height: 90px;font-size: 22px; white-space: nowrap;">&nbsp;&nbsp;&nbsp;<img src="'+yyuc_jspath+'/img/load.gif" style="vertical-align: middle;"/>&nbsp;&nbsp;'+txt+'</span></div>');
			jQuery('body').append(div);
			div.css('zIndex',29891014);
			div.css('left',parseInt((jQuery(window).width()-div.width())/2));
			var top = parseInt(jQuery(window).scrollTop()+(jQuery(window).height()-div.height())/2);
			div.css('top',top);
		}	
	}
	
	/**
	 * 级联效果
	 * @returns
	 */
	_.getnextsel = function(o,tn,aw){
		var val = jQuery(o).val();
		if(val==jQuery(o).data('lastval')){
			return;
		}
		var uuid = jQuery(o).attr('seluuid');
		var index = parseInt(jQuery(o).attr('selindex'));
		var sel = jQuery('select[seluuid="'+uuid+'"]');
		
		sel.each(function(){
			var ind = parseInt(jQuery(this).attr('selindex'));
			if(ind<=index){
				sel = sel.not(this);
				return;
			}
			jQuery(this).find('option').each(function(i){
				if(i>0){
					jQuery(this).remove();
				}
			});
		});
		jQuery(o).data('lastval',val);
		if(jQuery.trim(val)!=''){
			_.ajaxcache('/@system/ajax-getselvt.html',{tn:tn,pid:val,aw:aw},function(m){
				for(var i=0;i<m.length;i++){
					sel.eq(0).append('<option value="'+m[i].id+'">'+m[i].name+'</option>');
				}
			});
		}
	}

	//表单校验
	jQuery.extend(jQuery.fn,{
		validations:function(){
			//返回需要被验证的jq对象
			return this.find('[required="required"],[YYUCVAL]');
		},tovalidate:function(fun){
			if(this.val()==''){
				//非空验证不通过直接返回false
				if(this.is('[required="required"]')){
					fun.apply(this[0],[false]);
				}else{
					fun.apply(this[0],[true]);
				}			
				return;
			}
			var valstr = jQuery.trim(this.attr('YYUCVAL'));
			if(valstr!=''){
				valstrs = valstr.split('ONE@ANOTHER');
				var val_arr = [];
				var uniques = false;//是否需要唯一性验证
				var uniquemsg = '';
				for(var i=0;i<valstrs.length;i++){
					var thevals = valstrs[i];
					if(jQuery.trim(thevals)==''){
						continue;
					}
					var the_arr = thevals.split('REG@MSG');
					if(the_arr[0].indexOf('YYUCUNIQUE')===0){
						//唯一性验证
						uniques = the_arr[0].split('@');
						uniquemsg = the_arr[1];
					}else{
						var reg = eval(the_arr[0]);
						if(!reg.test(this.val())){
							val_arr[val_arr.length] = the_arr[1];
						}
					}
				}
				
				if(val_arr.length>0){
					//不进行唯一性验证 直接返回验证错误
					fun.apply(this[0],[val_arr]);
				}else if(uniques!==false){
					//其他都正确只差唯一性验证
					if(uniques.length<4){
						uniques[uniques.length]=' ';
					}
					uniques[uniques.length] = this.val();
					_.ajaxjson('/@system/ajax-dbuniquecheck.html',uniques,function(m){
						if(m=='no'){
							val_arr[val_arr.length] = uniquemsg;
							fun.apply(this,[val_arr]);
						}else{
							fun.apply(this,[true]);
						}
					},null,this[0]);
				}else{
					fun.apply(this[0],[true]);
				}
			}else{
				fun.apply(this[0],[true]);
			}		
		},validate:function(fun){
			var valform = this;
			var valres = [];
			var hasvalnum = 0;
			var jqls = this.validations();
			jqls.each(function(){
				jQuery(this).tovalidate(function(m){
					hasvalnum++;
					if(m!==true){
						var valreso = {};
						valreso.e = this;
						valreso.m = m;
						valres[valres.length] = valreso;
					}
				});
			});
			var readytoend = function(){
				if(jqls.size()==hasvalnum){
					fun.apply(valform[0],[valres]);
				}else{
					setTimeout(function(){
						readytoend();
					},200);
				}
			};
			readytoend();
		}
	});
	
	//表单校验的简单写法
	_.validate = function(jform,funall,funone){
		jform.validate(function(valres){
			if(funone){
				for(var i=0;i<valres.length;i++){
					var re = valres;
					var re0 = false;
					if(re.m != false){
						re0 = re.m[0];
					}
					funone(jQuery(re.e),re0,re.m);
				}
			}
			if(funall){
				funall(valres.length>0);
			}
		});
	};
	
	_.htmlspecialchars  =  _.h = function(string, quote_style, charset, double_encode) {
		  var optTemp = 0,
		    i = 0,
		    noquotes = false;
		  if (typeof quote_style === 'undefined' || quote_style === null) {
		    quote_style = 2;
		  }
		  string = string.toString();
		  if (double_encode !== false) { // Put this first to avoid double-encoding
		    string = string.replace(/&/g, '&amp;');
		  }
		  string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');

		  var OPTS = {
		    'ENT_NOQUOTES': 0,
		    'ENT_HTML_QUOTE_SINGLE': 1,
		    'ENT_HTML_QUOTE_DOUBLE': 2,
		    'ENT_COMPAT': 2,
		    'ENT_QUOTES': 3,
		    'ENT_IGNORE': 4
		  };
		  if (quote_style === 0) {
		    noquotes = true;
		  }
		  if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
		    quote_style = [].concat(quote_style);
		    for (i = 0; i < quote_style.length; i++) {
		      // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
		      if (OPTS[quote_style[i]] === 0) {
		        noquotes = true;
		      }
		      else if (OPTS[quote_style[i]]) {
		        optTemp = optTemp | OPTS[quote_style[i]];
		      }
		    }
		    quote_style = optTemp;
		  }
		  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
		    string = string.replace(/'/g, '&#039;');
		  }
		  if (!noquotes) {
		    string = string.replace(/"/g, '&quot;');
		  }

		  return string;
		}
	
	_.toggleFullScreen = function () {
		  if ((document.fullScreenElement && document.fullScreenElement !== null) ||
		      (!document.mozFullScreen && !document.webkitIsFullScreen)) {
		    if (document.documentElement.requestFullScreen) {
		      document.documentElement.requestFullScreen();
		    } else if (document.documentElement.mozRequestFullScreen) {
		      document.documentElement.mozRequestFullScreen();
		    } else if (document.documentElement.webkitRequestFullScreen) {
		      document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
		    }
		    _.fullscreen = true;
		  } else {
		    if (document.cancelFullScreen) {
		      document.cancelFullScreen();
		    } else if (document.mozCancelFullScreen) {
		      document.mozCancelFullScreen();
		    } else if (document.webkitCancelFullScreen) {
		      document.webkitCancelFullScreen();
		    }
		    _.fullscreen = false;
		 }
	}
}(jQuery,window.YYUC));

//以下为修正一些基本浏览器的友好兼顾
jQuery(function(){
	/**
	 * 批量替换 一般字符替换 replaceall效率高 
	 * 特殊字符用replaceAll 效率稍低
	 */
	String.prototype.replaceAll = function(token, newToken, ignoreCase ) {
	    var _token;
	    var str = this + "";
	    var i = -1;
	    if ( typeof token === "string" ) {
	        if ( ignoreCase ) {
	            _token = token.toLowerCase();
	            while( (
	                i = str.toLowerCase().indexOf(
	                    token, i >= 0 ? i + newToken.length : 0
	                ) ) !== -1
	            ) {
	                str = str.substring( 0, i ) +
	                    newToken +
	                    str.substring( i + token.length );
	            }

	        } else {
	            return this.split( token ).join( newToken );
	        }
	    }
	return str;
	};
	
	//select title修正 
	var yyuc_set_sel_title = function(o){
		var opt_title = jQuery.trim(jQuery(o).find('option:selected').attr('title'));
		opt_title = opt_title == '' ? jQuery.trim(jQuery(o).find('option:selected').text()) : opt_title;
		opt_title = opt_title == '' ? jQuery(o).attr('title') : opt_title;
		jQuery(o).attr('title',opt_title);
	};
	jQuery('select').each(function(){
		yyuc_set_sel_title(this);
		jQuery(this).change(function(){
			yyuc_set_sel_title(this);
		});
	});
	
	//parseInt修正
	if(!window.parseInt2){
		window.parseInt2 = window.parseInt;
	}	
	window.parseInt = function(m,n){
		m = ''+m;
		while(m.indexOf('0')===0){
			m = m.substr(1);
		}
		if(m==''){
			return 0;
		}
		var jg = window.parseInt2(m,n);
		if(jg+''=='NaN'){
			return 0;
		}
		return jg;
	}
	
	
	//select的text一并提交
	var yyuc_set_sel_text = function(o){
		var name = jQuery(o).attr('yyuc_autotext');
		var text = jQuery(o).find('option:selected').text();
		text = text == '' ? jQuery.trim(jQuery(o).find('option:selected').attr('title')) : text;
		jQuery('input[name="'+name+'"]').val(text);
	}
	jQuery('select[yyuc_autotext]').each(function(){
		jQuery(this).after('<input type="hidden" name="'+name+'"/>');
		yyuc_set_sel_text(this);
		jQuery(this).change(function(){
			yyuc_set_sel_text(this);
		});
	});
	
	//根据标签加载控件
	//YYUC.yyucobjnum = jQuery('[rel="yyuc"]').size();
	//YYUC.yyucobjnow = -1;
	YYUC.loadyyucobj();
});
window._ = window.YYUC;