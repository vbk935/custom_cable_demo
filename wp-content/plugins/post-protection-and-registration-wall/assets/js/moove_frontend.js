

(function($){

	var updateModalRedirect = '';
	var SignupFormValidation = function() {

		var instance = this;
		instance.settings = {
			errorElement: 'span',
			errorPlacement: function(error, element){
				error.appendTo(element.parent());
			},
			highlight: function(element, errorClass, validClass) {
			    $(element).parent().addClass(errorClass).removeClass(validClass);
			},
			unhighlight: function(element, errorClass, validClass) {
			    $(element).parent().removeClass(errorClass).addClass(validClass);
			},
			rules: {
				"moove_name": "required",
				"surname": "required",
				"email": {
					required: true,
					email: true,
					remote: {
						url: moove_front_end_scripts.ajaxurl,
						type: 'post',
						data: {
							action: 'check_email',
							email_address: function() {
								return $("#moove_register_form #email").val();
							}
						}
					}
				},
				"pwd": {
					required: true,
					minlength: 8,
				},
				"pwdc": {
					required: true,
					minlength: 8,
					equalTo: "#pwd"
				},
			},
			messages: {
				"moove_name": {
					"required": 	moove_front_end_scripts.validationoptions['Sign-up_first-name_required']
				},
				"surname": {
					"required": 	moove_front_end_scripts.validationoptions['Sign-up_last-name_required']
				},
				"email": {
					"required": 	moove_front_end_scripts.validationoptions['Sign-up_email_required'],
					"email": 		moove_front_end_scripts.validationoptions['Sign-up_email_invalid-email'],
					"remote": 		moove_front_end_scripts.validationoptions['Sign-up_email_already-registered']
				},
				"pwd": {
					"required": 	moove_front_end_scripts.validationoptions['Sign-up_password_required'],
					"minlength": 	moove_front_end_scripts.validationoptions['Sign-up_password_min-length']
				},
				"pwdc": {
					"required": 	moove_front_end_scripts.validationoptions['Sign-up_password_required'],
					"minlength": 	moove_front_end_scripts.validationoptions['Sign-up_password_min-length'],
					"equalTo": 		moove_front_end_scripts.validationoptions['Sign-up_password_equal-to']
				},
			}
		}
		instance.init = function() {
			var registerForm = $("#moove_register_form");
			if (registerForm.length == 1) {
				registerForm.validate(instance.settings);
			}
		}
		instance.init();
	}


	var LoginFormValidation = function() {
		var instance = this;
		instance.submitButton = null;
		instance.settings = {
			errorElement: 'span',
			submitHandler: function(form) {
				instance.submitButton = $(form).find('button[type="submit"]');
				$('span.login-message').remove();
				instance.submitButton.after('<span class="login_message" style="margin-left: 30px;">'+moove_front_end_scripts.validationoptions['Login_ajax-message_signing-in']+'</span>');
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: moove_front_end_scripts.ajaxurl,
					data: {
						action: 'moove_ajaxlogin',
						email: $(form).find('input[name="email"]').val(),
						password: $(form).find('input[name="password"]').val(),
						security: $(form).find('input[name="security"]').val(),
						remember: $(form).find('input[name="remember"]').is(':checked') ? 'on': 0,
						redirect: $(form).find('input[name="redirect_to"]').val(),
					},
					success: instance.loginResponse
				})
				return false;
			},
			highlight: function(element, errorClass, validClass) {
			    $(element).parent().addClass(errorClass).removeClass(validClass);
			},
			unhighlight: function(element, errorClass, validClass) {
			    $(element).parent().removeClass(errorClass).addClass(validClass);
			},
			rules: {
				email: {
					"required": true,
					"email": true
				},
				password: "required"
			},
			messages: {
				email: {
					"required": 	moove_front_end_scripts.validationoptions['Login_email_required'],
					"email": 		moove_front_end_scripts.validationoptions['Login_email_invalid-email'],
				},
				password: {
					"required": 	moove_front_end_scripts.validationoptions['Login_password_required']
				}
			}
		}
		instance.loginResponse = function(data) {
			$('span.login_message').remove();

			if (data.login === false) {
				instance.submitButton.after('<span class="login_message" style="margin-left: 30px;">'+moove_front_end_scripts.validationoptions['Login_ajax-message_invalid-login']+'</span>');
			} else {
				if ($.trim(data.redirect) != '') {
					location.href = data.redirect;
				} else {
					location.href = '/';
				}
			}
		}
		instance.validate = function(selector) {

			var loginForm = $(selector);
			if (loginForm.length > 0) {
				loginForm.validate(instance.settings);
			}
		}

		instance.init = function() {
			instance.validate('#moove-login-form');
		}

		instance.init();
	}
	var PasswordReset = function() {
		var instance = this;
		instance.validate = function(selector) {
			$(selector).validate({
				errorElement: 'span',
				submitHandler: function(form) {
					var theform = $(form);
					$("span.reset-error").remove();
					$.ajax({
						type: 'post',
						dataType: 'json',
						url: moove_front_end_scripts.ajaxurl,
						data: {
							action: 'moove_reset_password',
							email: theform.find('input[name="lost-mail"]').val(),
							security: $('.login-form-ajax').find('input[name="security"]').val()
						},
						success: function(msg) {
							if (msg.success === false) {
								theform.find('input[name="lost-mail"]').after("<span class='error reset-error'>"+moove_front_end_scripts.validationoptions['Login_lost-mail_nonexistent-email']+"</span>");
							} else {
								$('.moove-protection-login-container .login-part').addClass('reset-confirm-on');
								$('.moove-protection-login-container .reset-password-part').addClass('reset-confirm-on');
								$('.moove-protection-login-container .reset-confirm-part').addClass('reset-confirm-on');
								$('span.resetemail').text($('input[name="lost-mail"]').val());
							}
						}
					});
				},

				highlight: function(element, errorClass, validClass) {
			    	$(element).parent().addClass(errorClass).removeClass(validClass);
			   	},

				unhighlight: function(element, errorClass, validClass) {
				    $(element).parent().removeClass(errorClass).addClass(validClass);
				},
				rules: {
					"lost-mail": {
						required: true,
						email: true
					},
				},
				messages: {
					"lost-mail": {
						"required": moove_front_end_scripts.validationoptions['Login_lost-mail_required'],
						"email": 	moove_front_end_scripts.validationoptions['Login_lost-mail_invalid-email']
					}
				}
			});
		}
		instance.init = function() {
			instance.validate('#moove-password-reset.passwordreset');
		}
		instance.init();
	}

	var ResetFormValidation = function() {

		var instance = this;
		instance.settings = {
			errorElement: 'span',
			rules: {
				"password": {
					required: true,
					minlength: 8
				},
				"password2": {
					required: true,
					minlength: 8,
					equalTo: "#reset-password"
				}
			},

			messages: {
				"password": {
					"required": 	moove_front_end_scripts.validationoptions['Reset_password_required'],
					"minlength": 	moove_front_end_scripts.validationoptions['Reset_password_min-length']
				},
				"password2": {
					"required": 	moove_front_end_scripts.validationoptions['Reset_password_required'],
					"minlength": 	moove_front_end_scripts.validationoptions['Reset_password_min-length'],
					"equalTo": 		moove_front_end_scripts.validationoptions['Reset_password_equal-to']
				}
			}
		};
		instance.init = function() {
			var pwr = $("#PasswordResetForm");
			if (pwr.length == 1) {
				pwr.validate(instance.settings);
			}
		}
		instance.init();
	}

	$(document).ready(function(){

		// Signup form validation
		var sv = new SignupFormValidation();
		var lv = new LoginFormValidation();
		var pr = new PasswordReset();
		var rv = new ResetFormValidation();

        $('.moove-modal-dialog').on( "click", 'a.close' , function(e) {
        	e.preventDefault();
        	$('.moove-modal-dialog').removeClass('modal-open');
        });

        $('.moove-protection-login-container').on("click", "a.forgot-password", function(e){
        	e.preventDefault();
        	$('.moove-protection-login-container .login-part').addClass('reset-on');
        	$('.moove-protection-login-container .reset-password-part').addClass('reset-on');
        	$('.moove-protection-login-container .reset-confirm-part').addClass('reset-on');

        });

        $('.moove-protection-login-container').on("click","a.back-to-login", function(e){
        	e.preventDefault();
        	$('.moove-protection-login-container .login-part').removeClass('reset-on').removeClass('reset-confirm-on');
        	$('.moove-protection-login-container .reset-password-part').removeClass('reset-on').removeClass('reset-confirm-on');
        	$('.moove-protection-login-container .reset-confirm-part').removeClass('reset-on').removeClass('reset-confirm-on');
        });
    }); // end document ready

})(jQuery);

