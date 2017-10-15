jQuery( document ).ready(function() {
    jQuery('#moba-copy-button').click(function() {
        jQuery('#moba-copy-url').select();
        document.execCommand('copy');
    });
    jQuery('#moba-copy-url').focus(function() {
        jQuery('#moba-copy-url').select();
    });
});