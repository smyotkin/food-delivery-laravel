$(document).ready(function () {
    $('.ru-phone_format').mask("+7 999 999-99-99");

    $('.update_user :input').on('keyup change', function() {
        checkUserForm();
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
