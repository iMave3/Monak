$(document).ready(function () {
    // Přepnutí zobrazení košíku
    $('#shopCartIcon').click(function (event) {
        event.stopPropagation();
        $('#cartSubmenu').toggle();
        $('#submenu').hide(); // Skryje submenu uživatele
        $('#searchBox').hide(); // Skryje vyhledávání
    });

    // Zavření košíku, když klikneme mimo
    $(document).click(function (event) {
        if (!$(event.target).closest('#cartSubmenu').length && !$(event.target).closest('#shopCartIcon').length) {
            $('#cartSubmenu').hide();
        }
    });

    // Přepnutí zobrazení uživatelského submenu
    $('#iconAndNameDiv').click(function (e) {
        e.stopPropagation();
        $('#submenu').toggle();
        $('#cartSubmenu').hide();
        $('#searchBox').hide(); // Skryje vyhledávání
    });

    // Zavření submenu uživatele, když klikneme mimo
    $(document).click(function (e) {
        if (!$(e.target).closest('#iconAndNameDiv').length && !$(e.target).closest('#submenu').length) {
            $('#submenu').hide();
        }
    });

    // Přepnutí vyhledávacího boxu
    $('#findIcon').click(function (e) {
        e.stopPropagation();
        $('#searchBox').toggle(); // Přepne viditelnost vyhledávacího boxu
        $('#cartSubmenu').hide();
        $('#submenu').hide();
    });

    // Zavření vyhledávacího boxu, když klikneme mimo
    $(document).click(function (e) {
        if (!$(e.target).closest('#searchBox').length && !$(e.target).closest('#findIcon').length) {
            $('#searchBox').hide();
        }
    });

    // Přesměrování na stránku s výsledky hledání
    $('#searchButton').click(function () {
        window.location = "{{ search_url }}" + '?name=' + $('#searchInput').val();
    });
});
