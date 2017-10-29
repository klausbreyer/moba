var post_id = 0;
var attachments = [];

function moba_message(message) {
    jQuery('.messages').show();
    jQuery('.messages').append(message + '<br />');
}


function moba_errors(message) {
    jQuery('.errors').show();
    jQuery('.errors').append(message + '<br />');
}


function moba_before_process() {
    attachments = [];
    post_id = 0;

    jQuery('.errors').hide();
    jQuery('.errors').empty();
    jQuery('.messages').hide();
    jQuery('.messages').empty();
    jQuery('form input, form select, form textarea').attr('disabled', true);

    moba_message('Starting upload, please wait..');
}


function moba_after_process() {
    attachments = [];
    post_id = 0;

    jQuery('form input, form select, form textarea').attr('disabled', false);
    jQuery('form input, form select, form textarea').val('');
    moba_message('Successfully finished!');
}

function moba_submit() {

    moba_before_process();

    if (jQuery('#title').val().length === 0) {
        moba_errors('Please insert title!');
        return false;
    }
    if (jQuery('#content').val().length === 0) {
        moba_errors('Please insert content!');
        return false;
    }
    if (jQuery(':file').get(0).files.length === 0) {
        moba_errors('Please select files!');
        return false;
    }

    moba_create_post_and_init_upload();
}

function moba_create_post_and_init_upload() {
    var form = new FormData();
    form.append('action', 'moba_async_create_post');
    form.append('title', jQuery('#title').val());
    form.append('content', jQuery('#content').val());
    form.append('post_status', jQuery('#post_status').val());

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        processData: false,
        cache: false,
        async: true,
        data: form,
        contentType: false,
        success: function (data, textStatus, request) {
            post_id = data.data.post_id;
            moba_message('Post #' + post_id + ' created..');
            moba_upload_files(0);
        },
        error: function (data, textStatus, request) {
            console.log('error');
            console.log(data);
        }
    });
}


function moba_upload_files(i) {
    var file = jQuery(':file').get(0).files[i];
    var form = new FormData();
    form.append('action', 'moba_async_upload');
    form.append('post_id', post_id);
    form.append('file', file);

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        processData: false,
        cache: false,
        async: true,
        data: form,
        contentType: false,
        success: function (data, textStatus, request) {
            moba_message('Uploaded File ' + (i + 1) + '/' + jQuery(':file').get(0).files.length + '..');
            attachments.push(data.data);
            if (i + 1 < jQuery(':file').get(0).files.length) {
                moba_upload_files(++i);
            }
            else {
                moba_finalize_post();
            }

        },
        error: function (data, textStatus, request) {
            console.log('error');
            console.log(data);
        }
    });
}


function moba_finalize_post() {
    var form = new FormData();
    form.append('action', 'moba_async_finalize_post');
    form.append('title', jQuery('#title').val());
    form.append('content', jQuery('#content').val());
    form.append('post_status', jQuery('#post_status').val());

    for (var i = 0; i < attachments.length; i++) {
        form.append('attachment_ids[]', attachments[i].id);
        form.append('attachment_urls[]', attachments[i].url);
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
            moba_message('Thumbnail saved..');
            moba_message('Post updated..');
            moba_after_process();
        },
        error: function (data, textStatus, request) {
            console.log('error');
            console.log(data);
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
