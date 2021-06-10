$(document).ready(function () {
    $('.ru-phone_format').mask("+7 999 999-99-99");

    $('.update_user :input').on('keyup change', function() {
        checkUserForm();
    });

    $('.update_position :input').on('keyup change', function() {
        checkPositionForm();
    });
});

function checkUserForm() {
    $('#save_user').addClass('disabled btn-outline-secondary');

    if ($('#first_name').val().length < 2)
        return;

    if ($('#last_name').val().length < 2)
        return;

    // if (!phonePreg.test(userPhone.val()))
    //     return;

    $('#save_user').removeClass('disabled btn-outline-secondary').addClass('btn-outline-primary');
}

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
