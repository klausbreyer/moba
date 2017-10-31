var post_id = 0;
var attachment_urls = [];
var attachment_ids = [];

function moba_message(message) {
    jQuery('.messages').show();
    jQuery('.messages').append(message + '<br />');
}


function moba_errors(message) {
    jQuery('.errors').show();
    jQuery('.errors').append(message + '<br />');
}

function moba_lock_form() {
    jQuery('form input, form select, form textarea').attr('disabled', true);
}

function moba_free_form() {
    jQuery('form input, form select, form textarea').attr('disabled', false);
}

function moba_before_process() {
    attachment_urls = [];
    attachment_ids = [];
    post_id = 0;

    jQuery('.errors').hide();
    jQuery('.errors').empty();
    jQuery('.messages').hide();
    jQuery('.messages').empty();

    moba_lock_form();
    moba_message('Starting upload, please wait..');
}


function moba_after_process() {
    attachment_urls = [];
    attachment_ids = [];
    post_id = 0;


    jQuery('form input:text, form input:file, form textarea').val('');
    moba_free_form();
    moba_message('Successfully finished!');
}

function moba_submit() {

    moba_before_process();

    if (jQuery('input[name="title"]').val().length === 0) {
        moba_errors('Please insert title!');
        moba_free_form();
        return false;
    }
    if (jQuery('textarea[name="content"]').val().length === 0) {
        moba_errors('Please insert content!');
        moba_free_form();
        return false;
    }
    if (jQuery('input:file').get(0).files.length === 0) {
        moba_errors('Please select files!');
        moba_free_form();
        return false;
    }

    moba_create_post_and_init_upload();
}

function moba_create_post_and_init_upload() {
    var form = new FormData();
    form.append('action', 'moba_async_create_post');
    form.append('title', jQuery('input[name="title"]').val());
    form.append('content', jQuery('textarea[name="content"]').val());
    form.append('status', jQuery('select[name="status"]').val());
    form.append('_wpnonce', jQuery('input[name="_wpnonce"]').val());
    form.append('_wp_http_referer', jQuery('input[name="_wp_http_referer"]').val());

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        processData: false,
        cache: false,
        async: true,
        data: form,
        contentType: false,
        success: function (data, textStatus, request) {
            if (data.success === true) {
                post_id = parseInt(data.data.post_id);
                moba_message('Post #' + post_id + ' created..');
                moba_upload_files(0);

            }
            else {
                console.log('error');
                console.log(data);
                moba_errors(data.data.error);
                moba_free_form();
            }
        },
        error: function (data, textStatus, request) {
            console.log('error');
            console.log(data);
            moba_free_form();
        }
    });
}


function moba_upload_files(i) {
    var file = jQuery(':file').get(0).files[i];
    var form = new FormData();
    form.append('action', 'moba_async_upload');
    form.append('post_id', post_id);
    form.append('file', file);
    form.append('_wpnonce', jQuery('input[name="_wpnonce"]').val());
    form.append('_wp_http_referer', jQuery('input[name="_wp_http_referer"]').val());

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        processData: false,
        cache: false,
        async: true,
        data: form,
        contentType: false,
        success: function (data, textStatus, request) {
            if (data.success === true) {

                moba_message('Uploaded File ' + (i + 1) + '/' + jQuery(':file').get(0).files.length + ' ' + file.name + '..');
                attachment_urls.push(data.data.url);
                attachment_ids.push(data.data.id);
                if (i + 1 < jQuery(':file').get(0).files.length) {
                    moba_upload_files(++i);
                }
                else {
                    moba_finalize_post();
                }
            }
            else {
                console.log('error');
                console.log(data);
                moba_errors(data.data.error);
                moba_free_form();
            }

        },
        error: function (data, textStatus, request) {
            console.log('error');
            console.log(data);
            moba_free_form();
        }
    });
}


function moba_finalize_post() {
    var form = new FormData();
    form.append('action', 'moba_async_finalize_post');
    form.append('post_id', post_id);
    form.append('title', jQuery('input[name="title"]').val());
    form.append('content', jQuery('textarea[name="content"]').val());
    form.append('status', jQuery('select[name="status"]').val());
    form.append('_wpnonce', jQuery('input[name="_wpnonce"]').val());
    form.append('_wp_http_referer', jQuery('input[name="_wp_http_referer"]').val());

    for (var i = 0; i < attachment_urls.length; i++) {
        form.append('attachment_urls[]', attachment_urls[i]);
    }
    for (var i = 0; i < attachment_ids.length; i++) {
        form.append('attachment_ids[]', attachment_ids[i]);
    }

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        processData: false,
        cache: false,
        async: true,
        data: form,
        contentType: false,
        success: function (data, textStatus, request) {
            if (data.success === true) {
                moba_message('Thumbnail saved..');
                moba_message('Post updated..');
                moba_after_process();
            }
            else {
                console.log('error');
                console.log(data);
                moba_errors(data.data.error);
                moba_free_form();
            }
        },
        error: function (data, textStatus, request) {
            console.log('error');
            console.log(data);
            moba_free_form();
        }
    });
}


jQuery(document).ready(function () {
    jQuery('.url__button').click(function () {
        jQuery('.url__area').select();
        document.execCommand('copy');
    });
    jQuery('.url__area').focus(function () {
        jQuery('.url__area').select();
    });

});
