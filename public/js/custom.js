$(document).ready(function () {
    $('.ru-phone_format').mask("+7 999 999-99-99", {autoclear: false});

    $('body').on('keyup change', '.update_position :input', function() {
        checkPositionForm();
    });
});

function checkPositionForm() {
    $('#save').addClass('disabled btn-outline-secondary');

    if ($('#name').val().length < 2)
        return;

    if ($('#slug').val().length < 2)
        return;

    $('#save').removeClass('disabled btn-outline-secondary').addClass('btn-outline-primary');
}

function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}
