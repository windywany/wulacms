WULA                = {};
//登录验证
WULA.registerMobile = function () {
	$.validator.setDefaults({
		submitHandler: function () {
			$('#form_register').find(".account-btn").attr("disabled", true).val('注册中...').css('background', '#bcbcbd');
			$.ajax({
				type    : 'POST',
				url     : window.location.href,
				dataType: 'json',
				data    : $('#form_register').serialize(),
				success : function (ret) {
					if (ret.success) {
						window.location.href = ret.url;
					} else {
						$('#message').html(ret.errorMsg);
						$('#form_register').find(".account-btn").removeAttr("disabled").val('立即注册').css('background', '#06a3f9');
						if (ret.errorType === 1) {
							$('input[name="captcha"]').select();
						}
					}
				}
			});
		}
	});
	formRules = $.extend(formRules, {
		onkeyup       : false,
		errorElement  : "span",
		errorPlacement: function (error, element) {
			error.appendTo($('#message'));
		},
		focusInvalid  : true,
		success       : function (e) {
			e.addClass('valid');
		}
	});
	$('#form_register').validate($.prepareValidateRule(formRules));
};
WULA.autoAccountBg  = function () {
	$('.account-main').css('display', 'block');
	var accountWidth  = $('.account-main').width();
	var accountHeight = $('.account-main').height();
	var windowWidth   = $(window).width();
	var windowHeight  = $(window).height();
	var top           = (windowHeight - accountHeight) / 2 * 0.8;
	var left          = (windowWidth - accountWidth) / 2;
	$('.account-main').css('top', top).css('left', left);
	$('.account-bg').css('width', windowWidth + 'px').css('height', windowHeight + 'px');
	if (windowWidth < 1000) {
		$('.account-bg').css('width', '1000px');
	} else {
		$('.account-bg').css('width', '100%');
	}
	$('.account-bg').find('img').css('margin-top', '-10%');
};
$(function () {

	//登录时的表单验证
	WULA.registerMobile();

	//验证码换一换
	var captchaUrl = $('#captcha').attr('src');
	$('#captcha').click(function () {
		$('#captcha').attr('src', captchaUrl + '?_t=' + Math.random());
		$('.captcha').val('');
	});

	$('#vcode').click(function () {
		var href    = sendSmsUrl;
		var param   = {tpl: 'reg_verify'};
		param.phone = $('#phone').val();
		if (!/^1[34578]\d{9}$/.test(param.phone)) {
			alert('请填写手机号');
			return false;
		}
		if ($('#captcha_wrapper:visible').length > 0) {
			param.captcha = $('#captcha').val();
			if (!param.captcha) {
				alert('请填写图片验证码');
				return false;
			}
		}
		if ($(this).hasClass('disabled')) {
			return false;
		}
		$(this).attr('disabled', true).addClass('disabled').html('正在发送...');
		var timer = null, $this = $(this);
		$.getJSON(href, param, function (data) {
			if (data.success) {
				if (timer) {
					clearTimeout(timer);
				}
				var count = data.timeout;
				timer     = setInterval(function () {
					if (count == 0) {
						$this.removeAttr('disabled').removeClass('disabled').html('免费获取验证码');
						clearTimeout(timer);
					} else {
						$this.html(count + '秒后重发...');
						count--;
					}
				}, 1000);
			} else {
				$this.removeAttr('disabled').removeClass('disabled').html('免费获取验证码');
			}
		});
		return false;
	});

	//页面背景图片
	if ($('.account-main').length > 0) {
		WULA.autoAccountBg();
		window.onresize = function () {
			WULA.autoAccountBg();
		}
	}
});