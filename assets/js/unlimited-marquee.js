
jQuery(document).ready(function($) {
    $('.color-field').wpColorPicker(); // Initialize color pickers

    // Customize font direction select box
    $('#font_direction').selectmenu();

    // Customize scroll delay input
    $('#scroll_delay').spinner({
        min: 0, // Minimum value
        max: 100, // Maximum value
        step: 1, // Step increment
    });

    // Customize show/hide marquee checkbox
    $('#show_marquee').checkboxradio();

    // Add styles to improve appearance
    $('.ui-widget').css('font-size', '14px'); // Increase font size for consistency
    $('.ui-widget-content').css('background-color', '#f7f7f7'); // Set background color for consistency
    $('.ui-widget-header').css('background-color', '#337ab7'); // Set header background color
    $('.ui-spinner-input').css('width', '60px'); // Adjust spinner input width
});

document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('copy_shortcode_button').addEventListener('click', function () {
            var shortcodeInput = document.getElementById('marquee_shortcode');
            shortcodeInput.select();
            document.execCommand('copy');
            alert('Shortcode copied!');
        });
    });


document.addEventListener('DOMContentLoaded', function() {
    const hideCheckbox = document.getElementById('show_marquee_hide');
    const otherCheckboxes = document.querySelectorAll('[name="show_marquee[]"]:not(#show_marquee_hide)');

    hideCheckbox.addEventListener('change', function() {
        if (hideCheckbox.checked) {
            otherCheckboxes.forEach(function(checkbox) {
                checkbox.checked = false;
                checkbox.disabled = true;
            });
        } else {
            otherCheckboxes.forEach(function(checkbox) {
                checkbox.disabled = false;
            });
        }
    });

    otherCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            if (hideCheckbox.checked) {
                this.checked = false;
            }
        });
    });
});
