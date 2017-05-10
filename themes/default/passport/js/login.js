WULA               = {};
//登录验证
WULA.login         = function () {
	$.validator.setDefaults({
		submitHandler: function () {
			$('#message-wraper').hide();
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
						$('#message-wraper').show();
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