$(document).ready(function() {
    // When clicking on the shopping cart icon, toggle the cart submenu visibility
    $('#shopCartIcon').click(function(event) {
        event.stopPropagation(); // Prevent the click from propagating
        $('#cartSubmenu').toggle(); // Toggle the cart submenu visibility
        $("#shopCartIcon").css("filter", "drop-shadow(1px 1px 3px #7e7e7e)"); // Optional shadow effect on the icon

        // If the cart submenu is open, close the user submenu
        $('#submenu').hide();
    });

    // When clicking anywhere outside the cart submenu or shopping cart icon, close the cart submenu
    $(document).click(function(event) {
        if (!$(event.target).closest('#cartSubmenu').length && !$(event.target).closest('#shopCartIcon').length) {
            $('#cartSubmenu').hide(); // Hide the cart submenu if clicked outside
        }
    });

    // When clicking on the iconAndNameDiv, toggle the user submenu visibility
    $('#iconAndNameDiv').click(function(e) {
        e.stopPropagation(); // Prevent the click event from propagating to the document
        $('#submenu').toggle(); // Toggle the user submenu visibility

        // If the user submenu is open, close the cart submenu
        $('#cartSubmenu').hide();
    });

    // When clicking anywhere outside the iconAndNameDiv or user submenu, close the user submenu
    $(document).click(function(e) {
        if (!$(e.target).closest('#iconAndNameDiv').length && !$(e.target).closest('#submenu').length) {
            $('#submenu').hide(); // Hide the user submenu if clicked outside
        }
    });
});