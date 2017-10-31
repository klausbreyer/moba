<div class="moba">
    <h2>Mobile Batch Upload</h2>
    <div class="messages">
    </div>
    <div class="errors">

    </div>

    <form action="" method="post" enctype="multipart/form-data" class="moba__form"
          onsubmit="moba_submit(); return false;">
        <?php wp_nonce_field( 'moba-upload'); ?>
        <label>Title:</br>
            <input type="text" id="title" name="title"/></label><br/>
        <label>Content:</br>
            <textarea name="content" id="content"></textarea></label><br/>
        <label>Files (multiple):</br>
            <input type="file" name="upload[]" multiple/></label><br/>

        <label>Status:</br>
            <select name="status" id="status">
                <option>publish</option>
                <option>draft</option>
            </select>
        </label><br/>
        <label>Comments:</br>
            <select name="comment_status" id="comment_status">
                <option>open</option>
                <option>closed</option>
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