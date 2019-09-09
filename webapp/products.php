<?php include "include/header.php"; ?>

<h1>Products</h1>

<?php include "include/footer.php"; ?>



<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
        data = fetch('include/product.php', {
            method: 'POST',
            body: JSON.stringify({'task': 'list_all'})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            console.log(data);
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    });

</script>