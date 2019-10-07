<?php include "include/header.php"; ?>
<link rel="stylesheet" href="css/jobs.css">
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
                    <div class="input-group"><label for="new-job_id-text">Job Id</label><input type="text" id="new-job_id-text"  size="2"></div>
                    <div class="input-group"><label for="new-cust_id-text">Customer Id</label><input type="text" id="new-cust_id-text"  size="2"></div>
                    <div class="input-group"><label for="new-prod_id-text">Product Id</label><input type="text" id="new-prod_id-text" size="2"></div>
                    <div class="input-group"><label for="new-qty-text">Qty</label><input type="text" id="new-qty-text" size="2"></div>
                    <div class="input-group"><label for="new-notes-text">Notes</label><textarea id="new-notes-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" id="new-job-btn">Submit</button>
                </div>
            </div>
            <div id="openEditEmpModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header"></h2>
                    <div class="input-group"><label for="edit-job_id-text">Job Id</label><input type="text" id="edit-job_id-text"  size="2"></div>
                    <div class="input-group"><label for="edit-cust_id-text">Customer Id</label><input type="text" id="edit-cust_id-text"  size="2"></div>
                    <div class="input-group"><label for="edit-prod_id-text">Product Id</label><input type="text" id="edit-prod_id-text" size="2"></div>
                    <div class="input-group"><label for="edit-qty-text">Qty</label><input type="text" id="edit-qty-text" size="2"></div>
                    <div class="input-group"><label for="edit-notes-text">Notes</label><textarea id="edit-notes-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" id="edit-job-btn">Submit</button>
                    <button type="button" id="del-job-btn">Delete Job</button>
                </div>
            </div>
        </div>
        <table class='db-table'>
            <col class="tid-col">
            <col class="tjobid-col">
            <col class="tadd-col">
            <col class="tstart-col">
            <col class="tfinish-col">
            <col class="tcustomer-col">
            <col class="tproduct-col">
            <col class="tqty-col">
            <col class="tnotes-col">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Job Id</th>
                    <th>Date Entered</th>
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
            'job_id': $('#new-job_id-text').val(),
            'customer_id': $('#new-cust_id-text').val(),
            'product_id': $('#new-prod_id-text').val(),
            'qty': $('#new-qty-text').val(),
            'notes': $('#new-notes-text').val()
        };

        api_call(args);
        //reset text boxes
        $('#new-job_id-text').text('');
        $('#new-cust_id-text').text('');
        $('#new-prod_id-text').text('');
        $('#new-qty-text').text('');
        $('#new-notes-text').text('');
        //close modal
        window.location = '#close';
        reload_table_data();
    });

//todo: customer_id, product_id fields are displayed as names now, editing should be changed to process names instead of ids
    $(document).on('click', '.db-table tr', function() {
        window.location = '#openEditEmpModal';
        //set header to current name of employee
        $('#openEditEmpModal .modal-header').text($(this).find('td:eq(0)').text());
        //fill input with data for employee that was clicked on
        $('#edit-job_id-text').val($(this).find('td:eq(1)').text());
        $('#edit-cust_id-text').val($(this).find('td:eq(5)').text());
        $('#edit-prod_id-text').val($(this).find('td:eq(6)').text());
        $('#edit-qty-text').val($(this).find('td:eq(7)').text());
        $('#edit-notes-text').val($(this).find('td:eq(8)').text());
    });

    $('#edit-job-btn').click(function() {
        var args = {
            'task': 32,
            'id': $('#openEditEmpModal .modal-header').text(),
            'job_id': $('#edit-job_id-text').val(),
            'customer_id': $('#edit-cust_id-text').val(),
            'product_id': $('#edit-prod_id-text').val(),
            'qty': $('#edit-qty-text').val(),
            'notes': $('#edit-notes-text').val()
        };

        api_call(args);
        //reset text boxes
        $('#openEditEmpModal .modal-header').text('');
        $('#edit-job_id-text').text('');
        $('#edit-cust_id-text').text('');
        $('#edit-prod_id-text').text('');
        $('#edit-qty-text').text('');
        $('#edit-notes-text').text('');
        //close modal
        window.location = '#close';
        reload_table_data();
    });

    $('#del-job-btn').click(function() {
        var args = {'task': 33,
                    'id': $('#openEditEmpModal .modal-header').text()
                   };

        api_call(args);
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
                $('.db-table tbody').append('<tr><td>'+el['id']+
                                            '</td><td>'+el['job_id']+
                                            '</td><td>'+el['date_added']+
                                            '</td><td>'+el['date_started']+
                                            '</td><td>'+el['date_finished']+
                                            '</td><td>'+el['customer_name']+
                                            '</td><td>'+el['product_name']+
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