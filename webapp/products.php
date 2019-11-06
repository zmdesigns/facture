<?php include 'include/header.php'; ?>
<link rel="stylesheet" href="css/products.css">
</head>
<body class='products'>
<div class='container'>
    <div class='side-nav'>
        <?php include 'include/nav.php'; ?>
    </div>
    <div class='content'>
        <div class='content-header'>
            <h2>Products</h2>
        </div>
        <div class="edit-links">
            <a class="modal-link" href="#openNewProductModal">New Product</a>
            <div id="openNewProductModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header">New Product</h2>
                    <div class="input-group"><label for="new-product_id-text">Product Id</label><input type="text" id="new-product_id-text"  size="2"></div>
                    <div class="input-group"><label for="new-name-text">Product Name</label><input type="text" id="new-name-text" size="5"></div>
                    <div class="input-group"><label for="new-description-text">Description</label><textarea id="new-description-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" id="new-product-btn">Submit</button>
                </div>
            </div>
            <div id="openEditProductModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header"></h2>
                    <div class="input-group"><label for="edit-product_id-text">Product Id</label><input type="text" id="edit-product_id-text"  size="2"></div>
                    <div class="input-group"><label for="edit-name-text">Product Name</label><input type="text" id="edit-name-text" size="5"></div>
                    <div class="input-group"><label for="edit-description-text">Description</label><textarea id="edit-description-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" id="edit-product-btn">Submit</button>
                    <button type="button" id="del-product-btn">Delete Product</button>
                </div>
            </div>
        </div>
        <table class='db-table'>
            <col class="tprod_id-col">
            <col class="tname-col">
	        <col class="tdescrip-col">
            <thead>
                <tr>
                    <th>Product Id</th>
                    <th>Product Name</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        
    </div>
    <div class='footer'>
        <?php include 'include/footer.php'; ?>
    </div>
</div>
</body>

<script type='text/javascript'>
	$(document).ready(function() {
        reload_table_data();
    });

    $('#new-product-btn').click(function() {
        var args = {
            'task': 21,
            'product_id': $('#new-product_id-text').val(),
            'name': $('#new-name-text').val(),
            'description': $('#new-description-text').val()
        };

        api_call(args);
        //reset text boxes
        $('#new-product_id-text').text('');
        $('#new-name-text').text('');
        $('#new-description-text').text('');
        //close modal
        window.location = '#close';
    });

    $(document).on('click', '.db-table tr', function() {
        window.location = '#openEditProductModal';
        //set header to product_id
        $('#openEditProductModal .modal-header').text($(this).find('td:eq(0)').text());
        //fill input with data for employee that was clicked on
        $('#edit-product_id-text').val($(this).find('td:eq(0)').text());
        $('#edit-name-text').val($(this).find('td:eq(1)').text());
        $('#edit-description-text').val($(this).find('td:eq(2)').text());
    });

    $('#edit-product-btn').click(function() {
        var args = {
            'task': 22,
            'product_id': $('#openEditProductModal .modal-header').text(),
            'new_product_id': $('#edit-product_id-text').val(),
            'name': $('#edit-name-text').val(),
            'description': $('#edit-description-text').val()
        };

        api_call(args);
        //reset text boxes
        $('#openEditProductModal .modal-header').text('');
        $('#edit-product_id-text').text('');
        $('#edit-name-text').text('');
        $('#edit-description-text').text('');
        //close modal
        window.location = '#close';
    });

    $('#del-product-btn').click(function() {
        var args = {'task': 23,
                    'product_id': $('#openEditProductModal .modal-header').text()
                   };

        api_call(args);
        window.location = '#close';
    });

    function api_call(args) {
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify(args)
        }).then(function(data) {
            console.log(data); //todo: if page returns an error, let the user know
            reload_table_data();
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
            return false;
        });
        return true;
    }

    function reload_table_data() {
        //clear table body rows if any
        $('.db-table tbody').html('');

        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 20})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            var row = 1; //starts at 1 because header row is 0
            data.forEach(function(el) {
                $('.db-table tbody').append('<tr><td>'+el['product_id']+
                                  '</td><td>'+el['name']+
                                  '</td><td>'+el['description']+'</td></tr>');
                row += 1;
            });
            $('.db-table').DataTable();
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }

</script>