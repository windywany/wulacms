WULA               = {};
//登录验证
WULA.login         = function () {
	$.validator.setDefaults({
		submitHandler: function () {
			$('#form_login').find(".account-btn").attr("disabled", true).val('登录中...').css('background', '#bcbcbd');
			$.ajax({
				type    : 'POST',
				url     : window.location.href,
				dataType: 'json',
				data    : $('#form_login').serialize(),
				success : function (ret) {
					if (ret.success) {
						window.location.href = ret.url;
					} else {
						if (ret.count > 3) {
							$('#captcha_wrapper').show();
						}
						$('#message').html(ret.errorMsg);
						$('#form_login').find(".account-btn").removeAttr("disabled").val('立即登录').css('background', '#fb8526');
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
	$('#form_login').validate($.prepareValidateRule(formRules));
};
WULA.autoAccountBg = function () {
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
	WULA.login();

	//验证码换一换
	var captchaUrl = $('#captcha').attr('src');
	$('#captcha').click(function () {
		$('#captcha').attr('src', captchaUrl + '?_t=' + Math.random());
		$('.captcha').val('');
	});
	//页面背景图片
	if ($('.account-main').length > 0) {
		WULA.autoAccountBg();
		window.onresize = function () {
			WULA.autoAccountBg();
		}
	}
});