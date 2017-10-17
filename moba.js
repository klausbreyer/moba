jQuery( document ).ready(function() {
    jQuery('.url__button').click(function() {
        jQuery('.url__area').select();
        document.execCommand('copy');
    });
    jQuery('.url__area').focus(function() {
        jQuery('.url__area').select();
    });
});