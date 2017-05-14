$(document).ready(function() {
    $("button").click(function(event) {
        event.preventDefault();
        console.log(this);
        $(this).animate({ fontSize: '30px' });
    });
});
