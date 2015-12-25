jQuery(document).ready(function ($) {
    $('body').on('valid.bs.validator', function (event) {
        console.log(event);
    });
});