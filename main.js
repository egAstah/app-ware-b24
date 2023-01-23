$(document).ready(function () {
    $("h5").text("Основной склад");
    $("#saveRows").attr("data-btn", "ware");
    $.ajax({
        url: "./function.php",
        method: "post",
        dataType: "html",
        data: {event: "user-ware", dataWare: "ware"},
        success: function (data) {
            $(".product-table").html(data);
            $(".table").show();
        },
    });
    $('#updateWare').attr('data-ware', 'ware')
    $(document).on('click', '#folder-items', function (){
        let id = $(this).attr('data-id')
        $('.btn-folder').removeClass('active')
        $(this).addClass('active')
        $('.list-products').hide()
        $.ajax({
            url: "./function.php",
            method: "post",
            dataType: "html",
            data: {event: "section", id: id},
            success: function (data) {
                data = JSON.parse(data)
                data.forEach(function (item){
                    $('.list-products[id="'+item+'"]').show()
                })

            },
        });
    })
    $(document).on('click', '#updateWare', function () {
        if ($(this).attr('data-ware') === 'ware') {
            $.ajax({
                url: "./function.php",
                method: "post",
                dataType: "html",
                data: {event: "user-ware", dataWare: "ware"},
                success: function (data) {
                    $(".product-table").html(data);
                    $(".table").show();
                },
            });
        } else {
            $.ajax({
                url: "./function.php",
                method: "post",
                dataType: "html",
                data: {event: "user-ware", id: $("#user").val(), dataWare: "user"},
                success: function (data) {
                    $(".product-table").html(data);
                    $(".table").show();
                },
            });
        }
    })

    $(document).on("click", "#selectUser", function () {
        $(".product-table").html("");
        $('.btn-folder').removeClass('active')
        BX24.selectUser(function (res) {
            $("#user").val(res.id);
            $("h5").text("Склад сотрудника " + res.name);
            $("#saveRows").attr("data-btn", "user");
            $('#updateWare').attr('data-ware', 'user')
            $.ajax({
                url: "./function.php",
                method: "post",
                dataType: "html",
                data: {event: "user-ware", id: res.id, dataWare: "user"},
                success: function (data) {
                    $(".product-table").html(data);
                    $(".table").show();
                },
            });
        });
    });
    $(document).on("click", "#wareAll", function () {
        $("h5").text("Основной склад");
        $('.btn-folder').removeClass('active')
        $("#saveRows").attr("data-btn", "ware");
        $.ajax({
            url: "./function.php",
            method: "post",
            dataType: "html",
            data: {event: "user-ware", dataWare: "ware"},
            success: function (data) {
                $(".product-table").html(data);
                $(".table").show();
            },
        });
    });
    $(document).on("click", "#saveRows", function () {
        var arr = [];
        $(".list-products").each(function () {
            let id = $(this).attr("id");
            arr.push({
                name: $(this).attr("data-name"),
                id: id,
                count: $('.form-control[data-id="' + id + '"]').val(),
                type: $('.form-label[data-id="' + id + '"]').attr("data-type"),
            });
        });
        $.ajax({
            url: "./function.php",
            method: "post",
            dataType: "html",
            data: {
                event: "save-user-ware",
                user: $("#user").val(),
                arr: arr,
                dataWare: $("#saveRows").attr("data-btn"),
            },
            success: function (data) {
                // console.log(data)
                location.reload();
            },
        });
    });
    $(document).on("click", "#sendSoglasovanie", function () {
        var arr = [];
        $(".list-products-modal").each(function () {
            let id = $(this).attr("id");
            arr.push({
                name: $(
                    '.list-products-modal .form-label[data-id="' + id + '"]'
                ).text(),
                id: id,
                count: $(
                    '.list-products-modal .form-control[data-id="' + id + '"]'
                ).val(),
            });
        });
        $.ajax({
            url: "./function.php",
            method: "post",
            dataType: "html",
            data: {event: "soglas-ware", user: $("#user").val(), arr: arr, type: $('#saveRows').attr('data-btn')},
            success: function (data) {
                location.reload()
            },
        });
    });
});
