
$("form[data-ajax]").submit(function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        type: $(this).attr("method"),
        dataType: "json",
        statusCode: {
            400: function () {
                noti("error", "400 status code! user error");
            },
            500: function () {
                noti("error", "500 status code! server error");
            },
        },
        success: (data) => {
            if (!data) {
                noti("error", "400 status code! user error");
            } else {
                if (!data.href && data.href === undefined) {
                    noti(data.status, data.message);
                } else {
                    if (data.status == 'success') {
                        setInterval(() => {
                            window.location.href = data.href;
                        }, 700);
                    }
                    noti(data.status, data.message);
                }
            }
        },
        error: function (request) {
            noti("error", "server error");
        },
    });
})



function noti(status, message) {
    if (status == "success") {
        var title =
            `Thành Công`;
    } else {
        var title = `Thất bại`;
    }
    var date = new Date().getTime();
    $(".toasts_noti").append(`<div toasts-id="${date}" class="toasts ${status} show"><div class="container-1"><i class="fas fa-check-circle"></i></div><div class="container-2"><p>${title}</p><p>${message}</p></div><button>&times;</button></div>`);
    var load = setInterval(() => {
        clearInterval(load);
        $(`.toasts_noti .toasts[toasts-id="${date}"]`).remove();
    }, 1000)
}

