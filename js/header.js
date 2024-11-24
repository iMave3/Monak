$(document).ready(function () {
    // Kliknutí na #iconAndNameDiv zobrazí nebo skryje submenu
    $("#iconAndNameDiv").click(function (event) {
        event.stopPropagation(); // Zastaví propagaci události kliknutí
        $("#submenu").toggle(); // Přepne viditelnost submenu
        if ($("#submenu").css("display") == "block") {
            $("#figureIcon").addClass("figureIconOn");
        }
        if ($("#submenu").css("display") == "none") {
            $("#figureIcon").removeClass("figureIconOn");
        }
    });

    // Kliknutí kdekoliv na stránce zavře submenu, pokud je otevřené
    $(document).click(function () {
        if ($("#submenu").css("display") == "block") {
            $("#submenu").hide(); // Skryje submenu
        }
        $("#figureIcon").removeClass("figureIconOn");
    });

    // Zamezí zavření submenu při kliknutí uvnitř něj
    $("#submenu").click(function (event) {
        event.stopPropagation(); // Zastaví propagaci události kliknutí
    });

});