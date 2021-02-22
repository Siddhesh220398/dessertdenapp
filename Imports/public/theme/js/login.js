var Login = function () {
	var handleLogin = function() {
		$('.login-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                email: {
                    required: true,
                    email: true,
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
                }
            },
            messages: {
                email: {
                    required: "Email is required.",
                },
                password: {
                    required: "Password is required."
                }
            },
            invalidHandler: function (event, validator) {
                
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
        $('.login-form input').keypress(function (e) {
            if (e.which == 13) {
                if ($('.login-form').validate().form()) {
                    $('.login-form').submit();
                }
                return false;
            }
        });
	}

	var handleForgetPassword = function () {
		$('.forget-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            ignore: "",
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                email: {
                    required: "Email is required."
                }
            },
            invalidHandler: function (event, validator) {
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },
            submitHandler: function (form) {
                form.submit();
            }
        });

        $('.forget-form input').keypress(function (e) {
            if (e.which == 13) {
                if ($('.forget-form').validate().form()) {
                    $('.forget-form').submit();
                }
                return false;
            }
        });

	}
    
    return {
        init: function () {
            handleLogin();
            handleForgetPassword();
		    $.backstretch([
		        "images/login_bg/1.jpg",
		        "images/login_bg/2.jpg",
		        "images/login_bg/3.jpg",
		        "images/login_bg/4.jpg"
		        ], {
		          fade: 1000,
		          duration: 8000
		    	}
        	);
        }
    };
}();

$(document).ready(function() {
    Login.init();
});