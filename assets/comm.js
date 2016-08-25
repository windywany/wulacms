(function(z){z.module = {};})(jQuery);
(function(f){f.module.validator={};var g={},d=0;var c='<span class="icon-notice icon-error"></span>',b='<span class="icon-notice icon-success"></span>';var i={};i.noempty={exec:function(){return f.trim(this.val())!=""},error:"不能为空"};i.numberonly={exec:function(){return/^\d+$/i.test(f.trim(this.val()))},error:"必须是纯数字"};i.length={exec:function(){var l=arguments[0],k=arguments[1],m=f.trim(this.val()),j=true;if(l!==0&&m.length<l){j=false}if(k!==0&&m.length>k){j=false}return j},error:function(){var k=arguments[0],j=arguments[1];if(k!==0&&j!==0){if(k===j){return"长度是"+k+"个字符"}else{return"长度必须在"+k+"-"+j+"之间"}}if(k!==0){return"长度必须大于"+k}if(j!==0){return"长度必须小于"+j}},args:[0,0]};var e=function(j,m){g[j].errorContainer.empty();if(m===true){if(f.trim(this.val())==""){return}g[j].errorContainer.removeClass("fm-validator-failure");if(g[j].showSuccess){g[j].errorContainer.addClass("fm-validator-success");f(b).appendTo(g[j].errorContainer)}this.removeClass("fm-haserror");return}else{m=m||g[j].result.message;if(f.isArray(m)){m=(function(){var o=[];f.each(m,function(q,p){o.push(f.escapeHTML(p))});return o.join("<br>")})()}g[j].errorContainer.removeClass("fm-validator-success").addClass("fm-validator-failure");g[j].errorContainer.html(c+m);this.addClass("fm-haserror");return;var l=m.length*12;if(l>240){l=240}g[j].errorContainer.css("width",l)}var k=this.outerWidth()+10,n=(g[j].errorContainer.outerHeight()-this.outerHeight())/2;g[j].errorContainer.css({left:k,top:-n});g[j].errorContainer.find(".fm-validator-arrow-left").css("top",(g[j].errorContainer.innerHeight()-11)/2)};var h=function(j){g[j].errorContainer.removeClass("fm-validator-success").removeClass("fm-validator-failure")};var a=function(k){var j={};if(typeof k==="string"){if(!i[k]){return}j=f.extend(j,i[k])}else{if(typeof k.exec==="string"){if(!i[k.exec]){return}j=f.extend(j,i[k.exec]);delete k.exec;j=f.extend(j,k)}else{j=f.extend(j,k)}}if(f.inArray(j,this)==-1){this.push(j)}};f.extend(f.module.validator,{add:function(j,m,l){if(typeof j==="string"){j=f(j)}if(!j.length||!m){return}var k=j.data("validator");if(!k){k=j[0].type+"-"+(++d)}if(!g[k]){g[k]={id:j[0].id,type:j[0].type,validator:[],showSuccess:false};j.addClass("fm-validator").data("validator",k);if(!j.parent().children(".fm-validator-result").length){g[k].errorContainer=f('<div class="fm-validator-result"></div>').appendTo(j.parent())}else{g[k].errorContainer=j.parent().children(".fm-validator-result")}j.bind("blur",function(){f.module.validator.validate(j)});j.bind("focus",function(){f(this).removeClass("fm-haserror");g[k].errorContainer.removeClass("fm-validator-failure")})}l&&f.extend(g[k],l);if(f.isArray(m)){while(m.length){a.call(g[k].validator,m.shift())}}else{a.call(g[k].validator,m)}},validate:function(j,l){if(typeof j==="string"){j=f(j)}if(!j.length){return true}var k=j.data("validator");if(!k){return true}g[k].result=(function(){var m={success:true,message:""};for(var o=0,n=g[k].validator.length;o<n;o++){m.success=g[k].validator[o].exec.apply(j,g[k].validator[o].args||[]);if(!m.success){m.message=typeof g[k].validator[o].error==="string"?g[k].validator[o].error:g[k].validator[o].error.apply(j,g[k].validator[o].args||[]);break}}return m})();if(!g[k].result.success){!l&&e.call(j,k);return false}!l&&e.call(j,k,true);return true},validateAll:function(l){l=f(l);var j={},k;j.success=true;l.find(".fm-validator").each(function(){var m=f(this);j.success=f.module.validator.validate(m)&&j.success});return j.success},error:function(j,l){if(typeof j==="string"){j=f(j)}if(!j.length){return}var k=j.data("validator");if(!k){return}e.call(j,k,l)},errorByName:function(m,l){var j=f("input[name="+m+"]");if(!j.length){return}var k=j.data("validator");if(!k){return}e.call(j,k,l)},clear:function(j){if(typeof j==="string"){j=f(j)}if(!j.length){return}var k=j.data("validator");if(!k){return}h.call(j,k)}})})(jQuery);
;
(function(d){d.module.pstrength={};var b={scores:[25,45,64,95],common:["password","liverpool","letmein","monkey","abc123","dragon","baseball","welcome","password1","myspace1","woaini","iloveyou","521521","520520","5201314","1314520","1314521","201314","147258","147258369","159357","112358"],strinorder:"01234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",minchar:6};b.strreverse=b.strinorder.split("").reverse().join("");var a=function(g,e){var f=0;if(g.length<e.minchar){f=(f-100);return}if(e.strinorder.indexOf(g)>-1||e.strreverse.indexOf(g)>-1){f=-100;return}if(d.inArray(g.toLowerCase(),e.common)>-1){f=-200;return}if(d.unique(g.split("")).length===1){f=-300;return}if(g.length>=e.minchar&&g.length<=(e.minchar+2)){f=(f+6)}else{if(g.length>=(e.minchar+3)&&g.length<=(e.minchar+4)){f=(f+12)}else{if(g.length>=(e.minchar+5)){f=(f+18)}}}if(g.match(/[a-z]/)){f=(f+1)}if(g.match(/[A-Z]/)){f=(f+5)}if(g.match(/\d+/)){f=(f+5)}if(g.match(/(.*[0-9].*[0-9].*[0-9])/)){f=(f+7)}if(g.match(/.[!,@,#,$,%,^,&,*,?,_,~]/)){f=(f+5)}if(g.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)){f=(f+7)}if(g.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)){f=(f+2)}if(g.match(/([a-zA-Z])/)&&g.match(/([0-9])/)){f=(f+3)}if(g.match(/([a-zA-Z0-9].*[!,@,#,$,%,^,&,*,?,_,~])|([!,@,#,$,%,^,&,*,?,_,~].*[a-zA-Z0-9])/)){f=(f+3)}return f};var c=function(h,f,e){var g=d("#"+h+"-pstrength");if(e>0){if(e<=f.scores[0]){g.addClass("pstrength-weak");g.removeClass("pstrength-middle");g.removeClass("pstrength-strong")}else{if(e>f.scores[0]&&e<=f.scores[1]){g.addClass("pstrength-middle");g.removeClass("pstrength-weak");g.removeClass("pstrength-strong")}else{if(e>f.scores[1]&&e<=f.scores[2]){g.addClass("pstrength-strong");g.removeClass("pstrength-weak");g.removeClass("pstrength-middle")}}}}else{g.removeClass("pstrength-strong");g.removeClass("pstrength-weak");g.removeClass("pstrength-middle")}};d.extend(d.module.pstrength,{create:function(e,h){var h=d.extend(b,h);if(typeof e==="string"){e=d(e)}var g=e.attr("id");var f=d("#"+g+"-pstrength");if(!f.length){d('<div id="'+g+'-pstrength" class="pstrength-container"><div id="'+g+'-pstrength-bar" class="pstrength-bar clr"><div class="pstrength-bar-weak">弱</div><div class="pstrength-bar-middle">中</div><div class="pstrength-bar-strong">强</div></div></div>').appendTo(e.parent())}e.on("keyup",function(){var i=a(e.val(),h);c(g,h,i)})},checkPassword:function(f,g){var g=d.extend(b,g);if(typeof f==="string"){f=d(f)}var e=a(f.val(),g);return e},getErrormsg:function(e){if(e==-100){return"这个密码太简单了。"}if(e==-200){return"这个密码太常见了。"}if(e==-300){return"密码不能是连续的相同字符。"}return"这个密码太简单了。"}})})(jQuery);
;

var CommAjaxUploader = function(opts, uploader) {
	var ept = function(){};
	this.uploader = uploader;
	this.uploaderElm = opts.elem;
	var uploadedCb = opts.onUploaded || ept;
	var startCb = opts.onStart || ept;
	var errorCb = opts.onError || ept;
	var me = this;
	
	uploader.bind('FilesAdded', function(up, files) {
		startCb.call(me);
		var fs = up.files;
		up.disableBrowse(true);
		me.uploaderElm.addClass('uploading disabled');
		for ( var j in fs) {
			if (fs[j].id != files[0].id) {
				up.removeFile(fs[j]);
			}
		}
		me.uploader.start();
	});
	
	uploader.bind('FileUploaded', function(up, file, resp) {
		up.disableBrowse(false);
		me.uploaderElm.removeClass('uploading disabled');
		if (file.status == plupload.DONE) {
			try {
				var result = eval('(' + resp.response + ')');
				var rst = result.result;
				if (rst) {
					uploadedCb.call(me,result);
					return;
				} else if (result.error) {
					alert(result.error.message);
				} else {
					alert('无法解析服务器响应:<br/>' + resp.response);
				}
			} catch (e) {
				alert('无法解析服务器响应:<br/>' + resp.response);
			}
		} else {
			alert('出错啦.');
		}
		errorCb.call(me);
	});

	uploader.bind('Error', function(up, file) {
		up.disableBrowse(false);
		me.uploaderElm.removeClass('uploading disabled');
		alert(file.message);
		errorCb.call(me);
	});
};


