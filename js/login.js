$(document).ready(function () {
    // Když uživatel klikne do inputu, přesuneme a změníme barvu štítku (label) pro email
    $("input").on("focus", function () {
        $(this).prev("#emailLabel").animate({ top: "134px", left: "55px", color: "black" }, 200);
    });

    // Když uživatel klikne mimo input (ztratí fokus), a pokud je input prázdný, vrátíme štítek zpět
    $("input").on("blur", function () {
        if ($(this).val() === "") {
            $(this).prev("#emailLabel").animate({ top: "152px", left: "55px", color: "gray" }, 200);
        }
    });

    // Stejná akce jako pro email, ale pro input pro heslo
    $("input").on("focus", function () {
        $(this).prev("#passwordLabel").animate({ top: "173px", left: "55px", color: "black" }, 200);
    });

    // Stejná akce jako pro email, ale pro input pro heslo, pokud je ztracený fokus
    $("input").on("blur", function () {
        if ($(this).val() === "") {
            $(this).prev("#passwordLabel").animate({ top: "191px", left: "55px", color: "gray" }, 200);
        }
    });

    // Když uživatel klikne do inputu pro email, přesuneme a změníme barvu "required" štítku
    $(".emailInput").on("focus", function () {
        $(this).prev(".required").animate({ top: "-18px", left: "12px", color: "black" }, 200);
    });

    // Když uživatel klikne mimo input pro email, a pokud je prázdný, vrátíme "required" štítek zpět
    $(".emailInput").on("blur", function () {
        if ($(this).val() === "") {
            $(this).prev(".required").animate({ top: "0px", left: "5px", color: "gray" }, 200);
        }
    });
});
