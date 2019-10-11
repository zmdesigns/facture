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
                    <div class="input-group"><label for="new-cust_id-text">Customer</label><input type="text" id="new-cust_id-text"  size="10" list="customers"></div>
                    <datalist id="customers"></datalist> 
                    <div class="input-group"><label for="new-prod_id-text">Product</label><input type="text" id="new-prod_id-text" size="10" list="products"></div>
                    <datalist id="products"></datalist> 
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
                    <div class="input-group"><label for="edit-cust_id-text">Customer</label><input type="text" id="edit-cust_id-text"  size="10" list="customers"></div>
                    <div class="input-group"><label for="edit-prod_id-text">Product</label><input type="text" id="edit-prod_id-text" size="10" list="products"></div>
                    <div class="input-group"><label for="edit-qty-text">Qty</label><input type="text" id="edit-qty-text" size="2"></div>
                    <div class="input-group"><label for="edit-notes-text">Notes</label><textarea id="edit-notes-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" id="edit-job-btn">Submit</button>
                    <button type="button" id="del-job-btn">Delete Job</button>
                </div>
            </div>
        </div>
        <table class='db-table'>
            <col class="tjobid-col">
            <col class="tcustomer-col">
            <col class="tproduct-col">
            <col class="tqty-col">
            <col class="thrs-col">
            <thead>
                <tr>
                    <th>Job Id</th>
                    <th>Status</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Hours Worked</th>
                </tr>
            </thead>
            <tbody class="tcontent">
            </tbody>
        </table>
    </div>
    <div class='g-footer'>
        <?php include "include/footer.php"; ?>
    </div>
</div>
</body>

<script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
<script src='js/helpers.js'></script>

<script type='text/javascript'>
	$(document).ready(function() {
        reload_sorted_table();
        fill_datalist('#customers',40,'name');
        fill_datalist('#products',20,'name');
    });

    $('#new-job-btn').click(function() {
        var args = {
            'task': 31,
            'job_id': $('#new-job_id-text').val(),
            'customer_name': $('#new-cust_id-text').val(),
            'product_name': $('#new-prod_id-text').val(),
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
    });

    $(document).on('click', '.db-table .header', function() {
        $(this).nextUntil('.header').toggle();
    });

    $(document).on('click', '.db-table tr', function() {
        
        /*
        window.location = '#openEditEmpModal';
        //set header to current name of employee
        $('#openEditEmpModal .modal-header').text($(this).find('td:eq(0)').text());
        //fill input with data for employee that was clicked on
        $('#edit-job_id-text').val($(this).find('td:eq(1)').text());
        $('#edit-cust_id-text').val($(this).find('td:eq(5)').text());
        $('#edit-prod_id-text').val($(this).find('td:eq(6)').text());
        $('#edit-qty-text').val($(this).find('td:eq(7)').text());
        $('#edit-notes-text').val($(this).find('td:eq(8)').text());
        */
    });

    $('#edit-job-btn').click(function() {
        var args = {
            'task': 32,
            'job_id': $('#edit-job_id-text').val(),
            'customer_name': $('#edit-cust_id-text').val(),
            'product_name': $('#edit-prod_id-text').val(),
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
    });


    $('#del-job-btn').click(function() {
        var args = {'task': 33,
                    'id': $('#openEditEmpModal .modal-header').text()
                   };
        api_call(args);
        window.location = '#close';
    });

    function fill_datalist(datalist_id, api_task, db_column) {
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': api_task})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            data.forEach(function(el) {
                $(datalist_id).append('<option value="'+el[db_column]+'">'+el[db_column]+'</option');
            });
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }

    function reload_sorted_table() {
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 34})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            //iterate job_ids
            $.each(data, function (job_id, job_array) {

                //add header for job_id to table
                $('.tcontent').append('<tr class="header '+job_id+'"><td>Job# '+job_array[0]['job_id']+
                                      '</td><td class="h-status">0%'+
                                      '</td><td class="h-customer">'+
                                      '</td><td class="h-product">'+
                                      '</td><td class="h-qty">0'+
                                      '</td><td class="h-hrs"></td></tr>');

                
                //calculate hours worked for job_id
                data = fetch('include/api.php', {
                    method: 'POST',
                    dataType: 'text/plain',
                    body: JSON.stringify({'task': 13,
                                          'employee_id': '',
                                          'workstation_id': '',
                                          'job_id': job_id})
                }).then(response => response.text())
                .then(function(data) {
                    console.log(data);
                    $('.header.'+job_id).children('.h-hrs').text(data);
                });
                
                //iterate jobs with job_id
                for (var index in job_array) {
                    var job = job_array[index];
                    var status = get_job_status(job['date_started'],job['date_finished']);
                    $('.tcontent').append('<tr class="child-row '+job['job_id']+'"><td>'+job['job_id']+'-'+index+
                                            '</td><td>'+status+
                                            '</td><td>'+job['customer_name']+
                                            '</td><td>'+job['product_name']+
                                            '</td><td>'+job['qty']+'</td></tr>');

                    //update header row for job
                    var $header = $('.header').last();
                    //update status column1
                    var $h_stat = $header.children('.h-status');
                    var size = job_array.length;
                    if (status == 'Finished') {
                        var perc_i = $h_stat.text().indexOf('%');
                        if (perc_i !== -1) {
                            $h_stat.text($h_stat.text().substring(0,perc_i));
                            $h_stat.text(parseInt($h_stat.text()) + (100 / size));
                            $h_stat.text($h_stat.text() + '%');
                        }
                    }
                    
                    //update customer column
                    var $h_cust = $header.children('.h-customer');
                    $h_cust.text($h_cust.text() + ',' + job['customer_name']);
                    if ($h_cust.text().charAt(0) === ',') {
                        $h_cust.text($h_cust.text().substring(1));
                    }
                    //update product column
                    var $h_prod = $header.children('.h-product');
                    $h_prod.text($h_prod.text() + ',' + job['product_name']);
                    if ($h_prod.text().charAt(0) === ',') {
                        $h_prod.text($h_prod.text().substring(1));
                    }
                    //update qty column
                    var $h_qty = $header.children('.h-qty');
                    $h_qty.text(parseInt($h_qty.text()) + parseInt(job['qty']));
                }
            });
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }

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
                $('.db-table tbody').append('<tr><td>'+el['job_id']+
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
            reload_table_data();
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }
</script>