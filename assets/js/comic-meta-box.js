jQuery(document).ready(function($) {
    // --- Media uploader for Comic Page Image (on 'comic' post type edit screen) ---
    var comicPageImageFrame;

    $('#comixcore_comic_page_image_button').on('click', function(e) {
        e.preventDefault();

        if (comicPageImageFrame) {
            comicPageImageFrame.open();
            return;
        }

        comicPageImageFrame = wp.media({
            title: 'Select or Upload Comic Page Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        comicPageImageFrame.on('select', function() {
            var attachment = comicPageImageFrame.state().get('selection').first().toJSON();
            $('#comixcore_comic_page_image_id').val(attachment.id);
            $('#comixcore_comic_page_image_preview').html('<img src="' + attachment.url + '" style="max-width:150px; height:auto;" />');
            $('#comixcore_comic_page_image_remove_button').show();
        });

        comicPageImageFrame.open();
    });

    $('#comixcore_comic_page_image_remove_button').on('click', function() {
        $('#comixcore_comic_page_image_id').val('');
        $('#comixcore_comic_page_image_preview').html('');
        $(this).hide();
        return false;
    });

    if ($('#comixcore_comic_page_image_id').val() === '') {
        $('#comixcore_comic_page_image_remove_button').hide();
    }


    // --- Media uploader for Series Logo (on 'comic_series' taxonomy edit screen) ---
    var seriesLogoFrame;

    $('#comixcore_series_logo_button').on('click', function(e) {
        e.preventDefault();

        if (seriesLogoFrame) {
            seriesLogoFrame.open();
            return;
        }

        seriesLogoFrame = wp.media({
            title: 'Select or Upload Series Logo',
            button: {
                text: 'Use this logo'
            },
            multiple: false
        });

        seriesLogoFrame.on('select', function() {
            var attachment = seriesLogoFrame.state().get('selection').first().toJSON();
            $('#comixcore_series_logo_id').val(attachment.id);
            $('#comixcore_series_logo_preview').html('<img src="' + attachment.url + '" style="max-width:150px; height:auto;" />');
            $('#comixcore_series_logo_remove_button').show();
        });

        seriesLogoFrame.open();
    });

    $('#comixcore_series_logo_remove_button').on('click', function() {
        $('#comixcore_series_logo_id').val('');
        $('#comixcore_series_logo_preview').html('');
        $(this).hide();
        return false;
    });

    if ($('#comixcore_series_logo_id').val() === '') {
        $('#comixcore_series_logo_remove_button').hide();
    }


    // --- Media uploader for Issue Cover (on 'comic_issues' taxonomy edit screen) ---
    var issueCoverFrame;

    $('#comixcore_issue_cover_button').on('click', function(e) {
        e.preventDefault();

        if (issueCoverFrame) {
            issueCoverFrame.open();
            return;
        }

        issueCoverFrame = wp.media({
            title: 'Select or Upload Issue Cover Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        issueCoverFrame.on('select', function() {
            var attachment = issueCoverFrame.state().get('selection').first().toJSON();
            $('#comixcore_issue_cover_id').val(attachment.id);
            $('#comixcore_issue_cover_preview').html('<img src="' + attachment.url + '" style="max-width:150px; height:auto;" />');
            $('#comixcore_issue_cover_remove_button').show();
        });

        issueCoverFrame.open();
    });

    $('#comixcore_issue_cover_remove_button').on('click', function() {
        $('#comixcore_issue_cover_id').val('');
        $('#comixcore_issue_cover_preview').html('');
        $(this).hide();
        return false;
    });

    if ($('#comixcore_issue_cover_id').val() === '') {
        $('#comixcore_issue_cover_remove_button').hide();
    }
});