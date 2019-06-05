$(document).ready(function () {
    let acceptPath = $("#accept-notification-path").data("path");
    let rejectPath = $("#reject-notification-path").data("path");

    $(".accept-notifi").click(function () {
        let inv_id = $(this).data("inv");
        $.ajax({
            url: acceptPath,
            data: {
                id: inv_id
            },
            method: "POST",
            success: function () {
                $('ul li #invi-' + inv_id).fadeOut(300, function () {
                    $('ul li #invi-' + inv_id).remove();
                });
                minusOne();
            }
        });
    });

    $(".reject-notifi").click(function () {
        let inv_id = $(this).data("inv");
        $.ajax({
            url: rejectPath,
            data: {
                    id: inv_id
                },
            method: "POST",
            success: function () {
                $('ul li #invi-' + inv_id).fadeOut(300, function () {
                    $('ul li #invi-' + inv_id).remove();
                });
                minusOne();
            }
        });
    });

    function minusOne() {
        let howMany = $(".how-many").html();
        howMany = parseInt(howMany);
        howMany = howMany - 1;
        if (howMany < 1) {
            let noNotification = "<i class=\"fa fa-globe\"></i><b class=\"caret hidden-lg hidden-md\"></b><p class=\"hidden-lg hidden-md\">0<b class=\"caret\"></b></p>";
            $("li .dropdown-toggle").html(noNotification);
            noNotification = "<li style=\"min-width: 200px; margin: 1em 0 1.5em 4em \">No new notifications</li>"
            $("ul .dropdown-menu").html(noNotification);
        } else
            $(".how-many").text(howMany);
    }
});