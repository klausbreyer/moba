<div class="moba">
    <h2>Mobile Batch Upload</h2>
        <div class="messages">
        </div>
        <div class="errors">

        </div>

    <form action="" method="post" enctype="multipart/form-data" class="moba__form"
          onsubmit="moba_submit(); return false;">
        <label>Title:</br>
            <input type="text" id="title" name="title" value="<?php echo @$_POST['title'] ?>"/></label><br/>
        <label>Content:</br>
            <textarea name="content" id="content"><?php echo @$_POST['content'] ?></textarea></label><br/>
        <label>Files (multiple):</br>
            <input type="file" name="upload[]" multiple/></label><br/>

        <label>Status:</br>
            <select name="post_status" id="post_status">
                <option <?php echo @$_POST['post_status'] ? 'publish' : null ?> >publish</option>
                <option <?php echo @$_POST['post_status'] ? 'draft' : null ?>>draft</option>
            </select>
        </label><br/>
        <label>Done?
            <input type="submit" value="Submit">
        </label>
    </form>
    <div class="moba__url">
        <b>Direct Access Link (send this to your mobile phone):</b>
        <textarea readonly class="url__area"><?php menu_page_url( 'moba/interface.php' ); ?></textarea>

        <button class="url__button">Copy to Clipboard</button>
    </div>
</div>