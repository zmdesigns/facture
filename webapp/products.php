<?php include "include/header.php"; ?>

<h1>Products</h1>
<table class='db_table'>
    <thead>
        <tr>
            <th>id</th>
            <th>name</th>
            <th>description</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php include "include/footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
        data = fetch('include/product.php', {
            method: 'POST',
            body: JSON.stringify({'task': 'list_all'})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            data.forEach(function(el) {
                $('.db_table tbody').append('<tr><td>'+el['id']+
                                  '</td><td>'+el['name']+
                                  '</td><td>'+el['description']+
                                  '</td></tr>');
            });
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    });

</script>