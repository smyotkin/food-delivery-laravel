$(document).ready(function () {
    $('.ru-phone_format').mask("+7 999 999-99-99", {autoclear: false});

    setCursorToActive('.ru-phone_format');
});

function setCursorToActive(element) {
    let pos = 0;

    $('body').on('change', element, function(event) {
        pos = $.inArray('_', $(this).val());
    });

    $('body').on('click', element, function(event) {
        setCaretToPos($(this), pos);
        pos = $.inArray('_', $(this).val());
    });

    function setCaretToPos(input, pos) {
        input[0].setSelectionRange(pos, pos);
    }
}

function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));

    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function validateField(condition, selector, addClass = 'is-invalid') {
    if (condition) {
        selector.addClass(addClass);

        return false;
    }

    return true;
}

function checkValidation(fields) {
    let available = true;

    $.each(fields, function(key, value) {
        if (value === false) {
            available = false;
        }
    });

    return available;
}
