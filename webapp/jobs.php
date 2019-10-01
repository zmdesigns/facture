<?php include "include/header.php"; ?>
</head>
<body>
<div class='container'>
    <div class='g-header'>
        <?php include 'include/nav.php'; ?>

        <h1>Jobs</h1>
    </div>
    <div class='g-table'>
        <div class="edit-links">
            <a class="modal-link" href="#openNewJobModal">New Job</a>
            <div id="openNewJobModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header">New Job</h2>
                    <div class="input-group"><label for="new-cust_id-text">Customer Id</label><input type="text" id="new-cust_id-text"  size="2"></div>
                    <div class="input-group"><label for="new-prod_id-text">Product Id</label><input type="text" id="new-prod_id-text" size="2"></div>
                    <div class="input-group"><label for="new-qty-text">Qty</label><input type="text" id="new-qty-text" size="2"></div>
                    <div class="input-group"><label for="new-notes-text">Notes</label><textarea id="new-notes-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" id="new-job-btn">Submit</button>
                </div>
            </div>
        </div>
        <table class='db-table'>
            <col class="tadd-col">
            <col class="tstart-col">
            <col class="tfinish-col">
            <col class="tcustomer-col">
            <col class="tproduct-col">
            <col class="tqty-col">
            <col class="tnotes-col">
            <thead>
                <tr>
                    <th>Date Added</th>
                    <th>Date Started</th>
                    <th>Date Finished</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class='g-footer'>
        <?php include "include/footer.php"; ?>
    </div>
</div>
</body>

<script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>

<script type='text/javascript'>
	$(document).ready(function() {
        reload_table_data();
    });

    $('#new-job-btn').click(function() {
        var args = {
            'task': 31,
            'customer_id': $('#new-cust_id-text').val(),
            'product_id': $('#new-prod_id-text').val(),
            'qty': $('#new-qty-text').val(),
            'notes': $('#new-notes-text').val()
        };

        api_call(args);
        //reset text boxes
        $('#new-cust_id-text').text('');
        $('#new-prod_id-text').text('');
        $('#new-qty-text').text('');
        $('#new-notes-text').text('');
        //close modal
        window.location = '#close';
        reload_table_data();
    });

    function reload_table_data() {
        //clear table body rows if any
        $('.db-table tbody').html('');

        //load new content from database and fill table rows
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 30})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            data.forEach(function(el) {
                $('.db-table tbody').append('<tr><td>'+el['date_added']+
                                            '</td><td>'+el['date_started']+
                                            '</td><td>'+el['date_finished']+
                                            '</td><td>'+el['customer_id']+
                                            '</td><td>'+el['product_id']+
                                            '</td><td>'+el['qty']+
                                            '</td><td>'+el['notes']+'</td></tr>');
            });
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }

    function api_call(args) {
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify(args)
        }).then(function(data) {
            console.log(data); //todo: if page returns an error, let the user know
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }
</script>