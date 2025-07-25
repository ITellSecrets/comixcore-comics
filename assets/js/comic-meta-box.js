jQuery(document).ready(function($) {
    // Media uploader for Comic Page Image (using IDs to match meta-boxes.php)
    var comicPageImageFrame;

    // Use the correct button ID: comixcore_comic_page_image_button
    $('#comixcore_comic_page_image_button').on('click', function(e) {
        e.preventDefault();

        // If the media frame already exists, reopen it.
        if (comicPageImageFrame) {
            comicPageImageFrame.open();
            return;
        }

        // Create a new media frame
        comicPageImageFrame = wp.media({
            title: 'Select or Upload Comic Page Image',
            button: {
                text: 'Use this image'
            },
            multiple: false // Set to true if you want to allow multiple images
        });

        // When an image is selected in the media frame...
        comicPageImageFrame.on('select', function() {
            // Get media attachment details from the frame state
            var attachment = comicPageImageFrame.state().get('selection').first().toJSON();

            // Send the attachment id to our hidden input (using correct ID: comixcore_comic_page_image_id)
            $('#comixcore_comic_page_image_id').val(attachment.id);

            // Send the attachment URL to our image preview (using correct ID: comixcore_comic_page_image_preview)
            $('#comixcore_comic_page_image_preview').html('<img src="' + attachment.url + '" style="max-width:150px; height:auto;" />');

            // Show the remove image button (using correct ID: comixcore_comic_page_image_remove_button)
            $('#comixcore_comic_page_image_remove_button').show();
        });

        // Open the media frame
        comicPageImageFrame.open();
    });

    // Remove image button (using correct ID: comixcore_comic_page_image_remove_button)
    $('#comixcore_comic_page_image_remove_button').on('click', function() {
        // Clear the hidden input and preview (using correct IDs)
        $('#comixcore_comic_page_image_id').val('');
        $('#comixcore_comic_page_image_preview').html('');
        $(this).hide();
        return false;
    });

    // Handle initial state if image is already selected (using correct ID: comixcore_comic_page_image_id)
    if ($('#comixcore_comic_page_image_id').val() === '') {
        $('#comixcore_comic_page_image_remove_button').hide();
    }
});