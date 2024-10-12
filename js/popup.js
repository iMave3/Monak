import $ from "jquery";

$(function(){
    $('.popup .close').on('click', function(){
        $(this).parent().remove();
    });
});