$(document).ready(function () {
    // Kliknutí na objednávku
    $(".order-summary").click(function (e) {
        // Zavřeme všechny detaily kromě aktuálně kliknutého
        $(".order-details").not($(this).next()).slideUp();

        // Otevřeme / zavřeme detaily u kliknuté objednávky
        $(this).next(".order-details").slideToggle();

        // Zabráníme zavření při kliknutí na samotnou objednávku
        e.stopPropagation();
    });
});
