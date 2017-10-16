<?php require_once( dirname( __FILE__ ) . '/processing.php' ); ?>
<div class="moba">
    <h2>Mobile Batch Upload</h2>
	<?php if ( count( $messages ) > 0 ): ?>
        <div class="messages">
			<?php foreach ( $messages as $message ): ?>
				<?php echo $message; ?><br/>
			<?php endforeach; ?>
        </div>

	<?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label>Title:</br>
            <input type="text" name="title" value="<?php echo @$_POST['title'] ?>"/></label><br/>
        <label>Content:</br>
            <textarea name="content"><?php echo @$_POST['content'] ?></textarea></label><br/>
        <label>Files (multiple):</br>
            <input type="file" name="upload[]" multiple/></label><br/>
        <label>Status:</br>
            <select name="post_status">
                <option <?php echo @$_POST['post_status'] ? 'publish' : null ?> >publish</option>
                <option <?php echo @$_POST['post_status'] ? 'draft' : null ?>>draft</option>
            </select>
        </label><br/>
        <input type="submit" value="Submit">
    </form>

    <b>Direct Access Link (send this to your mobile phone):</b>
    <textarea readonly id="moba-copy-url"><?php menu_page_url( 'moba/interface.php' ); ?></textarea>

    <button id="moba-copy-button">Copy to Clipboard</button>
</div>