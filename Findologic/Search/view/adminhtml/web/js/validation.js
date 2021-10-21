require([
        'jquery',
        'mage/translate',
        'jquery/validate'],
    function ($) {
        $.validator.addMethod(
            'shop-key-format', function (v) {
                var letterNumber = /^[0-9a-zA-Z]+$/;
                if (v.match(letterNumber) && v.length == 32) {
                    return true;
                } else {
                    return false;
                }
            }, $.mage.__('Wrong format of shop key.'));
    }
);
