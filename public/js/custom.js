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
    let ajaxError = false;

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
        success: function (data) {
            $('.table_pagination', params.element).remove();

            if (params.search || params.page === 1) {
                params.element.html(data);
            } else {
                params.element.append(data);
            }
        },
        error: function(request) {
            let errorMsg = request.status === 500 || request.responseJSON.message.length === 0 ? 'Произошла неизвестная ошибка' : request.responseJSON.message;

            ajaxError = true;

            Swal.fire({
                title: 'Ошибка',
                text: errorMsg,
                icon: 'warning',
                confirmButtonText: 'Обновить',
                cancelButtonText: 'Отмена',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload(true);
                }
            });
        },
        complete: function() {
            $('#preloader').addClass('d-none');

            if (delay && ajaxError === false) {
                timeout = setTimeout(function() {
                    showListAjax(params, delay);
                }, delay);
            } else {
                clearTimeout(timeout);
            }
        }
    });

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

function wtime (name, title, disableSelector) {
    if (typeof name === "undefined" || name.length === 0) {
        return null;
    }

    var input = $('body input[name="' + name + '"]');

    // console.log(input);

    if (input.length === 0) {
        return null;
    }

    if (typeof title === "undefined" || title.length === 0) {
        title = 'Часы работы';
    }

    var params = {
            input: input,
            time: null,
            element: null
        },
        matrix = {
            mon: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
            tue: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
            wed: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
            thu: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
            fri: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
            sat: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
            sun: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
        },
        days = {
            mon: 'ПН',
            tue: 'ВТ',
            wed: 'СР',
            thu: 'ЧТ',
            fri: 'ПТ',
            sat: 'СБ',
            sun: 'ВС'
        },
        indexDays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
        isDisabled = input.prop('disabled'),
        isDrawed = false,
        isHeadInit = false,
        startSelectingObj = null,
        startFromSelected = false,
        startDay = '',
        startHour = 0;

    /**
     * Перерисовывает элемент
     * @param obj Объект элемента
     */
    var redrawElement = function (obj) {
        if (obj.hasClass('wtb_selected')) {
            obj.removeClass('wtb_selected')
        } else {
            obj.addClass('wtb_selected')
        }
        recountAndSave();
    };

    /**
     * Перерисовыет элементы по матрице
     * @param current Текущий объект
     */
    var redrawMatrix = function (current) {
        var currentDay = current.attr('data-day'),
            startDayIndex = indexDays.indexOf(startDay.toString()),
            currentDayIndex = indexDays.indexOf(currentDay.toString()),
            fromDay = startDay,
            toDay = currentDay,
            inRange = false,
            currentHour = parseInt(current.attr('data-hour')),
            fromHour = startHour,
            toHour = currentHour,
            days = [],
            hours = [],
            selectors = [];

        if (startDayIndex !== currentDayIndex) {
            if (startDayIndex > currentDayIndex) {
                fromDay = currentDay;
                toDay = startDay;
            }

            for (var i in indexDays) {
                if (indexDays[i] == fromDay) {
                    inRange = true;
                }

                if (inRange) {
                    days.push(indexDays[i]);
                }

                if (indexDays[i] == toDay) {
                    inRange = false;
                    break;
                }
            }
        } else {
            days.push(currentDay);
        }

        if (startHour !== currentHour) {
            if (startHour > currentHour) {
                fromHour = currentHour;
                toHour = startHour;
            }

            for (fromHour; fromHour <= toHour; fromHour++) {
                hours.push(fromHour);
            }
        } else {
            hours.push(currentHour);
        }

        for (var d in days) {
            for (var h in hours) {
                selectors.push(".wtb_dh[data-day=" + days[d] + "][data-hour=" + hours[h] + "]");
            }
        }

        if (startFromSelected) {
            params.element.find(selectors.join(', ')).removeClass('wtb_selected');
        } else {
            params.element.find(selectors.join(', ')).addClass('wtb_selected');
        }

    };

    /**
     * Активация чекбоксов
     */
    var checkActivate = function () {
        var days = params.time,
            hours = {
                0: [],
                1: [],
                2: [],
                3: [],
                4: [],
                5: [],
                6: [],
                7: [],
                8: [],
                9: [],
                10: [],
                11: [],
                12: [],
                13: [],
                14: [],
                15: [],
                16: [],
                17: [],
                18: [],
                19: [],
                20: [],
                21: [],
                22: [],
                23: []
            };

        for (var d in days) {
            for (var h in days[d]) {
                hours[days[d][h]].push(d);
            }

            if (days[d].length == 24) {
                params.element.find('input[name=wtb_row_' + d + ']').prop('checked', true);
            } else {
                params.element.find('input[name=wtb_row_' + d + ']').prop('checked', false);
            }
        }

        for (var h in hours) {
            if (hours[h].length == 7) {
                params.element.find('input[name=wtb_col_' + h + ']').prop('checked', true);
            } else {
                params.element.find('input[name=wtb_col_' + h + ']').prop('checked', false);
            }
        }
    };


    /**
     * Пересчитывает выделенные и сохраняет
     */
    var recountAndSave = function () {

        for (var id in indexDays) {
            var day = indexDays[id];
            params.time[day] = [];
            params.element.find('.wtb_selected[data-day=' + day + ']').each(function () {
                params.time[day].push(parseInt($(this).attr('data-hour')));
            });
        }



        params.input.val(JSON.stringify(params.time));
        checkActivate();
    };

    /**
     * Событие выделение
     * @param obj Объект выделения
     */
    var doSelecting = function (obj) {
        if (startSelectingObj === null) {
            return false;
        }
        redrawMatrix(obj);
    };

    /**
     * Отрисовывает модуль
     */
    var draw = function () {
        var html = '';

        try {
            params.time = JSON.parse(params.input.val());
            // console.log(JSON.parse(params.input.val()));
        } catch (e) {
            params.time = {
                mon: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
                tue: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
                wed: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
                thu: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
                fri: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
                sat: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
                sun: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]
            };
            params.input.val(JSON.stringify(params.time));
        }

        if (!isDrawed) {
            html += '<div id="' + name + '_wtime_block" class="wtime_block noselect form-group mb-0' + (isDisabled ? ' disabled' : '') + '">';
        }

        html += '<div class="wtb_container">';
        for (var day in matrix) {

            var dayMatrix = matrix[day],
                dayTime = params.time[day],
                dayOfWeek = days[day],
                isFirstInit = false;

            if (typeof dayTime === "undefined") {
                return false;
            }

            if (!isHeadInit) {
                html += '<div class="wtb_line wtb_hours">';

                html += '<div class="wtb_item wtb_blank"></div>';
                for (var index in dayMatrix) {
                    var hour = dayMatrix[index],
                        hourStr = hour.toString();

                    if (hour < 10) {
                        hourStr = '0' + hour;
                    }

                    html += '<div class="wtb_item wtb_hour wtb_c_' + hour + '"><label><strong>' + hourStr + '</strong><br><input type="checkbox" value="' + hour + '" name="wtb_col_' + hour + '" class="wtb_check_hour"></label></div>';
                }
                html += '</div>';

                isHeadInit = true;
            }

            html += '<div class="wtb_line">';

            for (var index in dayMatrix) {
                var hour = dayMatrix[index],
                    isSel = dayTime.indexOf(hour) !== -1;

                if (!isFirstInit) {
                    html += '<div class="wtb_item wtb_day wtb_r_' + day + '"><label><strong>' + dayOfWeek + '</strong><input type="checkbox" value="' + day + '" name="wtb_row_' + day + '" class="wtb_check_day"></label></div>';
                    isFirstInit = true;
                }

                html += '<div class="wtb_item wtb_dh wtb_' + day + '_' + hour + (isSel ? ' wtb_selected' : '') + '" data-day="' + day + '" data-hour="' + hour + '"></div>';
            }
            html += '</div>';

        }
        html += '</div>';

        if (!isDrawed) {
            html += '</div>';
            params.input.after(html);
            params.element = $('body #' + name + '_wtime_block');
        } else {
            params.element.html(html);
        }

        params.element.find('.wtb_dh').on('click', function () {
            if (isDisabled) {
                return false;
            }
            redrawElement($(this));
        });

        params.element.find('.wtb_dh').on('mousedown', function () {
            if (isDisabled) {
                return false;
            }
            startSelectingObj = $(this);
            startFromSelected = startSelectingObj.hasClass('wtb_selected');
            startDay = startSelectingObj.attr('data-day');
            startHour = parseInt(startSelectingObj.attr('data-hour'));
            params.element.find('.wtb_dh').on('mouseover', function () {
                doSelecting($(this));
            });
        });

        $('body').on('mouseup', function () {
            if (isDisabled) {
                return false;
            }
            startSelectingObj = null;
            startFromSelected = null;
            startDay = '';
            startHour = 0;
            recountAndSave();
        });

        params.element.find('input').on('click', function () {
            if (isDisabled) {
                return false;
            }
            var isChecked = $(this).is(':checked');

            if ($(this).hasClass('wtb_check_hour')) {
                var hour = parseInt($(this).val());
                if (isChecked) {
                    params.element.find('.wtb_dh[data-hour=' + hour + ']').addClass('wtb_selected')
                } else {
                    params.element.find('.wtb_dh[data-hour=' + hour + ']').removeClass('wtb_selected')
                }
            } else if ($(this).hasClass('wtb_check_day')) {
                var day = $(this).val();
                if (isChecked) {
                    params.element.find('.wtb_dh[data-day=' + day + ']').addClass('wtb_selected')
                } else {
                    params.element.find('.wtb_dh[data-day=' + day + ']').removeClass('wtb_selected')
                }
            }

            recountAndSave();
        });

        if (typeof disableSelector !== "undefined") {
            disableSelector.on('change', function () {
                if (!$(this).is(':checkbox')) {
                    return false;
                }

                if ($(this).is(':checked')) {
                    isDisabled = false;
                    params.element.removeClass('disabled');
                } else {
                    isDisabled = true;
                    params.element.addClass('disabled');
                }
            });
        }
        checkActivate();
    };

    draw();
    return params;
};
