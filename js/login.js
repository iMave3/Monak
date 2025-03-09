$(document).ready(function () {
    $("input").on("focus", function () {
        $(this).prev("#emailLabel").animate({ top: "134px", left: "55px", color: "black" }, 200);
    });

    $("input").on("blur", function () {
        if ($(this).val() === "") {
            $(this).prev("#emailLabel").animate({ top: "152px", left: "48px", color: "gray" }, 200);
        }
    });

    $("input").on("focus", function () {
        $(this).prev("#passwordLabel").animate({ top: "173px", left: "55px", color: "black" }, 200);
    });

    $("input").on("blur", function () {
        if ($(this).val() === "") {
            $(this).prev("#passwordLabel").animate({ top: "191px", left: "48px", color: "gray" }, 200);
        }
    });

    $(".emailInput").on("focus", function () {
        $(this).prev(".required").animate({ top: "-18px", left: "12px", color: "black" }, 200);
    });

    $(".emailInput").on("blur", function () {
        if ($(this).val() === "") {
            $(this).prev(".required").animate({ top: "0px", left: "5px", color: "gray" }, 200);
        }
    });
});
