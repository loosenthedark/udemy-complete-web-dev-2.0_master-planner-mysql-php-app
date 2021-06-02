<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $(".show-other-form").click(function() {
        $("#form-login-wrapper").toggle();
        $("#form-signup-wrapper").toggle();
    });
    $(".planner").fadeTo(5000, 0.97).on("input", function() {
        var request = $.ajax({
            url: "update-db.php",
            method: "POST",
            data: {
                content: $(".planner").val()
            }
        });

        request.fail(function(jqXHR, textStatus) {
            alert("Request failed: " + textStatus);
        });
    });
});
</script>