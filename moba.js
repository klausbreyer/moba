var post_id = 0;
var attachments = [];
jQuery(document).ready(function () {
    jQuery('.url__button').click(function () {
        jQuery('.url__area').select();
        document.execCommand('copy');
    });
    jQuery('.url__area').focus(function () {
        jQuery('.url__area').select();
    });

});

function debugFiles() {
    console.log(jQuery(":file").get());

    if (jQuery("#title").val().length === 0) {
        alert('Please insert title!');
        return false;
    }
    if (jQuery("#content").val().length === 0) {
        alert('Please insert content!');
        return false;
    }
    if (jQuery(":file").get(0).files.length === 0) {
        alert('Please select files!');
        return false;
    }

    create_post_and_init_upload();
}

function create_post_and_init_upload() {
    var form = new FormData();
    form.append('action', 'moba_async_create_post');
    form.append("title", jQuery("#title").val());
    form.append("content", jQuery("#content").val());
    form.append("post_status", jQuery("#post_status").val());

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        processData: false,
        cache: false,
        async: true,
        data: form,
        contentType: false,
        success: function (data, textStatus, request) {
            console.log('success');
            // console.log(data.data.post_id);
            post_id = data.data.post_id;
            upload(0);
        },
        error: function (data, textStatus, request) {
            console.log('error');
            console.log(data);
        }
    });
}


function upload(i) {
    console.log('upload');
    var form = new FormData();
    form.append('action', 'moba_async_upload');
    form.append('post_id', post_id);
    form.append("file", jQuery(":file").get(0).files[i]);

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        processData: false,
        cache: false,
        async: true,
        data: form,
        contentType: false,
        success: function (data, textStatus, request) {
            console.log('success');

            console.log(data);
            attachments.push(data.data);
            console.log(attachments);
            if (i + 1 < jQuery(":file").get(0).files.length) {
                upload(++i);
            }
            else {
                finalize_post();
            }

        },
        error: function (data, textStatus, request) {
            console.log('error');
            console.log(data);
        }
    });
}


function finalize_post() {
    console.log('finalize_post');
    var form = new FormData();
    form.append('action', 'moba_async_finalize_post');
    form.append("title", jQuery("#title").val());
    form.append("content", jQuery("#content").val());
    form.append("post_status", jQuery("#post_status").val());
    console.log(attachments);
    //hacky

    //have to think about this: https://stackoverflow.com/questions/16104078/appending-array-to-formdata-and-send-via-ajax
    form.append("attachments", JSON.stringify(attachments));

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        processData: false,
        cache: false,
        async: true,
        data: form,
        contentType: false,
        success: function (data, textStatus, request) {
            console.log('success');
            console.log(data);

        },
        error: function (data, textStatus, request) {
            console.log('error');
            console.log(data);
        }
    });
}
