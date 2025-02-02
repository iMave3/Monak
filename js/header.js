$(document).ready(function() {
    // Otevření submenu při kliknutí na košík
    $('#shopCartIcon').click(function(event) {
        event.stopPropagation(); // Zabraňuje propagačnímu efektu kliknutí na dokument
        $('#submenu').toggle(); // Přepíná zobrazení submenu
    });

    // Zavření submenu při kliknutí mimo submenu nebo ikonu
    $(document).click(function(event) {
        if (!$(event.target).closest('#shopCartIcon, #submenu').length) {
            $('#submenu').hide(); // Skrytí submenu
        }
    });

    // Zabrání zavření submenu při kliknutí přímo na něj
    $('#submenu').click(function(event) {
        event.stopPropagation();
    });
});
