/**
 * check for password contains atleast one Uppercase letter one lowercase letter one number and one symbol
 */
$.validator.addMethod("uplownumsym", function (value) {    
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]/.test(value)
});

/**
 * check for password contains at least one letter and one number 
 */
$.validator.addMethod("letternum", function (value) {    
    return /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]/.test(value)
});

/**
 * check for password contains at least one letter, one number and one special character
 */
$.validator.addMethod("letternumsym", function (value) {    
    return /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]/.test(value)
});

/**
 * check for password contains at least one uppercase letter, one lowercase letter and one number
 */
$.validator.addMethod("uplownum", function (value) {    
    return /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]/.test(value)
});

/**
 * Check to allow only alphabets and numbers
 */
$.validator.addMethod("alphanum", function(value, element) {
	return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
}); 

/**
 * Check to allow only alphabets
 */
$.validator.addMethod("alphaonly", function(value, element) {
    return this.optional(element) || value == value.match(/^[a-zA-Z]+$/);
 });

$.validator.addMethod("alphaspecial", function(value, element) {
    return this.optional(element) || value == value.match(/^[a-zA-Z.,\b]+$/);
 });



/* start jquery validation methos */
$.validator.addMethod("not_empty", function(value, element) {
    return this.optional(element) || /\S/.test(value);
}, "Only space is not allowed.");

$.validator.addMethod("valid_email", function(value, element) {
    return this.optional(element) || /^([\w-\.]+@([\w-]+\.)+[\w-]{2,})?$/.test(value);
}, "Please enter a valid email address.");

$.validator.addMethod("no_space", function(value, element) { 
    return value.indexOf(" ") < 0 && value != "";
}, "Space is not allowed.");

$.validator.addMethod("alpha_numeric", function(value, element) {
    return this.optional(element) || /^[a-zA-Z0-9\s]+$/.test(value);
}, "This field may only contain letters, numbers and space.");

$.validator.addMethod("lettersonly", function(value, element) {
  return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
}, "Please enter only letters."); 

$.validator.addMethod("not_equal", function(value, element, param) {
  return this.optional(element) || value != $(param).val();
}, "Please specify a different value.");

$.validator.addMethod("valid_url", function(value, element) {
  return this.optional(element) || /^[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=.]+$/gm.test(value);
}, "Please enter valid link.");

$.validator.addMethod('imageWidth', function(value, element, width) {
    return $(element).data('imageWidth') == width || $(element).data('imageWidth') == undefined;
}, function(width, element) {
    return "Image width must be equal to " + width + " px.";
});

$.validator.addMethod('imageHeight', function(value, element, height) {
    return $(element).data('imageHeight') == height || $(element).data('imageHeight') == undefined;
}, function(height, element) {
    return "Image height must be equal to " + height + " px.";
});
/* end jquery validation methods */
