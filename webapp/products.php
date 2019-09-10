<?php include 'include/header.php'; ?>

<h1>Products</h1>
<table class='db-table'>
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
<form id="new-product-form">
    <h4>New Product</h4>
    <div class="input-group">
        <div class="input-name">Name</div>
        <input id="name-field" type="text">
    </div>
    <div class="input-group">
        <div class="input-name">Description</div>
        <textarea id="description-field"></textarea>
    </div>
    <div class="input-group">
        <button id='new-btn' type='button'>New Product</button>
    </div>
</form>

<?php include 'include/footer.php'; ?>

<script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>

<script type='text/javascript'>
	$(document).ready(function() {
        data = fetch('include/product.php', {
            method: 'POST',
            body: JSON.stringify({'task': 'list_all'})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            data.forEach(function(el) {
                $('.db-table tbody').append('<tr><td>'+el['id']+
                                  '</td><td>'+el['name']+
                                  '</td><td>'+el['description']+
                                  '</td></tr>');
            });
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    });

    $('#new-btn').click(function() {
        data = fetch('include/product.php', {
            method: 'POST',
            body: JSON.stringify({'task': 'new',
                                  'prod_name': $('#name-field').val(),
                                  'description': $('#description-field').val()
                                 })
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            console.log(data);
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    });

</script>