<?php include "include/header.php"; ?>
<link rel="stylesheet" href="css/jobs.css">
</head>
<body class='jobs'>
    <div class='container'>
        <div class='side-nav'>
            <?php include 'include/nav.php'; ?>
        </div>

        <div class="content">
            <div class='content-header'>
                <h2>Jobs</h2>
            </div>
            <div class="edit-links">
                <a class="modal-link" id="new-job-link" href="#openNewJobModal">New Job</a>
                <div id="openNewJobModal" class="modal-dialog">
                    <div>
                        <a href="#close" title="Close" class="close">X</a>
                        <h2 class="modal-header">New Job</h2>
                        <label for="job_id-text">Job Id</label><input type="text" id="job_id-text">
                        <label for="customer-sel">Customer</label><select id="customer-sel"></select>
                        <label for="product-sel">Product</label><select id="product-sel"></select>
                        <label for="qty-text">Qty</label><input type="text" id="qty-text">
                        <button type="button" id="new-job-btn">Submit</button>
                    </div>
                </div>

            </div>

            <table class='job-table'>
                <col class="job_id-col">
                <col class="customer-col">
                <col class="product_id-col">
                <col class="product_name-col">
                <col class="product_qty-col">
                <col class="product_hrs-col">
                <thead>
                    <tr>
                        <th>Job</th>
                        <th>Customer</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Product Qty</th>
                        <th>Product Hours</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div id="productModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header"></h2>
                    <table class='product-table'>
                        <col class="workstation-col">
                        <col class="employee-col">
                        <col class="date-col">
                        <col class="time-col">
                        <thead>
                            <tr>
                                <th>Workstation</th>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Time Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class='total-hrs'></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class='footer'>
            <?php include "include/footer.php"; ?>
        </div>
    </div>
</body>

<script type='text/javascript'>
    $(document).ready(function() {
        reload_content();
    });

    $(document).on('click', '#new-job-link', function() {
        //fill customer and product select boxes
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 40})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            data.forEach(function(el) {
                $('#customer-sel').append('<option value="'+el['customer_id']+'">'+el['name']+'</option>');
            });
        });

        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 20})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            data.forEach(function(el) {
                $('#product-sel').append('<option value="'+el['product_id']+'">'+el['name']+'</option>');
            });
        });
    });

    $(document).on('click', '#new-job-btn', function() {
        var args = {
            'task': 31,
            'job_id': $('#job_id-text').val(),
            'customer_name': $('#customer-sel option:selected').text(),
            'product_name': $('#product-sel option:selected').text(),
            'qty': $('#qty-text').val(),
            'notes': 'none'
        };

        api_call(args);
        //reset text boxes
        $('#job_id-text').text('');
        $('#qty-text').text('');
        //close modal
        window.location = '#close';
    });

    $(document).on('click', '.job-table tbody tr', function() {
        var job_id = $(this).find('.tjob_id').text();
        var product_id = $(this).find('.tprod_id').text();
        var product_name = $(this).find('.tprod_name').text();
        var qty = $(this).find('.tprod_qty').text();
        var hrs = $(this).find('.tprod_hrs').text();
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 15,
                                  'job_id': job_id,
                                  'product_id':product_id})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            console.log(data);
            gen_product_modal(job_id,product_name,product_id,qty,hrs,data);
        });
    });

    function gen_product_modal(job_id, product_name, product_id, qty, hrs, log_array) {
        //header
        $('#productModal .modal-header').text(job_id+': '+product_name+' - '+product_id+' ('+qty+')');

        //clear previous product-table data
        var table = $('.product-table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'excel',
                'csv',
                {
                    extend: 'print',
                    customize: function(window) {
                        $(window.document.body).css('background-color','#fff');
                        $(window.document.body).css('color','#000');
                    }
                }
            ]
        });
        table.clear().draw();

        //fill product-table with log data
        $.each(log_array, function(id, log_detail) {
            
            $.each(log_detail, function(i, log_data) {
                var log_time = breakdown_time(log_data['hours']);
                var d = new Date(log_data['start'].split(' ')[0]);
                var date_str = format_date(d);
                
                table.row.add($('<tr><td>'+id+
                              '</td><td>'+log_data['employee']+
                              '</td><td>'+date_str+
                              '</td><td>'+log_time['hours']+' hours '+log_time['mins']+' minutes '+
                              '</td></tr>')).draw();
            });
        });

        total = breakdown_time(hrs);
        $('.total-hrs').text('Total: '+total['hours']+' hours '+total['mins']+' minutes ');
        
        window.location = '#productModal';
    }

    function reload_content() {
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 34})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            //clear previous job-table data
            var table = $('.job-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel',
                    'csv',
                    {
                        extend: 'print',
                        customize: function(window) {
                            $(window.document.body).css('background-color','#fff');
                            $(window.document.body).css('color','#000');
                        }
                    }
                ]
            });
            table.clear();
            //iterate job_ids
            $.each(data, function (job_id, job_array) {
                gen_job_detail(job_array,table);
            });
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }

    function gen_job_detail(job,table) {
        $.each(job, function(i, product) {
            table.row.add($('<tr><td class="tjob_id">'+product['job_id']+
                            '</td><td class="tcustomer">'+product['customer_name']+
                            '</td><td class="tprod_id">'+product['product_id']+
                            '</td><td class="tprod_name">'+product['product_name']+
                            '</td><td class="tprod_qty">'+product['qty']+
                            '</td><td class="tprod_hrs">'+product['hours']+
                            '</td></tr>')).draw();
        });
    }


    function api_call(args) {
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify(args)
        }).then(function(data) {
            console.log(data); //todo: if page returns an error, let the user know
            reload_content();
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
            return false;
        });
        return true;
    }
</script>

</body>
</html>