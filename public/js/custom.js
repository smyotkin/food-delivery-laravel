$(document).ready(function () {
    $('.ru-phone_format').mask("+7 999 999-99-99", {autoclear: false});

    setCursorToActive('.ru-phone_format');
});

function setCursorToActive(element) {
    let pos = 0;
    let body = $('body');

    body.on('change', element, function(event) {
        pos = $.inArray('_', $(this).val());
    });

    body.on('click', element, function(event) {
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

let timeout = 0;
/*
    Функция формирования списка данных, с прелоадером, указанием поискового запроса и страницы)
 */
function showListAjax(params, delay = 0)
{
    $.ajax({
        type: 'GET',
        data: {
            page: params.page,
            query: params.query,
        },
        url: params.route,
        beforeSend: function () {
            $('#preloader').removeClass('d-none');
        },
        complete: function() {
            $('#preloader').addClass('d-none');
        },
        success: function (data) {
            $('.table_pagination', params.element).remove();

            if (params.search || params.page === 1) {
                params.element.html(data);
            } else {
                params.element.append(data);
            }
        }
    });

    if (delay) {
        timeout = setTimeout(function() {
            showListAjax(params, delay);
        }, delay);
    } else {
        clearTimeout(timeout);
    }
}

function updateTableList(pageId, route, delay = 0)
{
    let cookieName = pageId + '_query_str';
    let search = $('#' + pageId + '-search');
    let getSearchCookie = getCookie(cookieName);

    let listParams = {
        query: search.val(),
        search: false,
        route: route,
        page: 1,
        element: $('#' + pageId + '_ajax')
    };

    if (getSearchCookie) {
        listParams.query = getSearchCookie;

        search.val(getSearchCookie);
    }

    showListAjax(listParams, delay);

    search.on('keyup', function () {
        document.cookie = cookieName + '=' + encodeURIComponent($(this).val());

        let value = $(this).val();

        listParams.search = value.length >= 0;
        listParams.query = value;
        listParams.page = 1;

        if (listParams.search) {
            showListAjax(listParams);
        }
    });

    $(document).on('click', '.table_pagination a', function(event) {
        event.preventDefault();

        listParams.search = false;
        listParams.page = $(this).attr('href').split('page=')[1];

        showListAjax(listParams);
    });
}
