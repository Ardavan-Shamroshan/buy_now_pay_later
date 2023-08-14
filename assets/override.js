window.addEventListener('load', function () {

    // store tabs variables
    let tabs = document.querySelectorAll('ul.nav-tabs > li');

    for (let i = 0; i < tabs.length; i++) {
        tabs[i].addEventListener('click', switchTab)
    }

    function switchTab(event) {
        event.preventDefault();

        document.querySelector('ul.nav-tabs > li.active').classList.remove('active');
        document.querySelector('.tab-pane.active').classList.remove('active');
        let clickedTab = event.currentTarget;
        let anchor = event.target;
        let activePaneID = anchor.getAttribute('href');

        clickedTab.classList.add('active');
        document.querySelector(activePaneID).classList.add('active');

    }
});


// jquery to upload image in media widget .php

jQuery(document).ready(function ($) {
    $(document).on('click', '.js-image-upload', function (e) {
        e.preventDefault();
        let $button = $(this);

        let file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select or Upload an Image',
            library: {
                type: 'image' // mime type
            },
            button: {
                text: 'Select Image'
            },
            multiple: false
        });

        file_frame.on('select', function () {
            let attachment = file_frame.state().get('selection').first().toJSON();
            $button.siblings('.image-upload').val(attachment.url);
        });

        file_frame.open();
    });
});
