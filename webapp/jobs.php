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

        <div class='footer'>
            <?php include "include/footer.php"; ?>
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
                    </table>
                </div>
            </div>
    </div>

<script src='js/helpers.js'></script>

<script type='text/javascript'>
    $(document).ready(function() {
        reload_content();

    });

    $(document).on('click', '.job-table tbody tr', function() {
        var job_id = $(this).find('.tjob_id').text();
        var product_id = $(this).find('.tprod_id').text();
        var product_name = $(this).find('.tprod_name').text();
        var qty = $(this).find('.tprod_qty').text();
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 15,
                                  'job_id': job_id,
                                  'product_id':product_id})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            console.log(data);
            gen_product_modal(job_id,product_name,product_id,qty,data);
        });
    });

    function gen_product_modal(job_id, product_name, product_id, qty, log_array) {
        //header
        $('#productModal .modal-header').text(job_id+': '+product_name+' - '+product_id+' ('+qty+')');

        //clear previous product-table data
        $('#productModal .product-table tbody').empty();

        //fill product-table with log data
        $.each(log_array, function(id, log_detail) {
            
            $.each(log_detail, function(i, log_data) {
                var log_time = breakdown_time(log_data['hours']);
                $('#productModal .product-table tbody').append('<tr><td>'+id+
                                                               '</td><td>'+log_data['employee']+
                                                               '</td><td>'+log_data['start']+
                                                               '</td><td>'+log_time['hours']+' hours '+log_time['mins']+' minutes '+log_time['secs']+' secounds'+
                                                               '</td></tr>');
            });
        });
        window.location = '#productModal';
    }

    function reload_content() {
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 34})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            //iterate job_ids
            $.each(data, function (job_id, job_array) {
                gen_job_detail(job_array);
            });
            $('.job-table').DataTable();
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }

    function gen_job_detail(job) {

        $.each(job, function(i, product) {
            $('.job-table tbody').append('<tr><td class="tjob_id">'+product['job_id']+
                                         '</td><td class="tcustomer">'+product['customer_name']+
                                         '</td><td class="tprod_id">'+product['product_id']+
                                         '</td><td class="tprod_name">'+product['product_name']+
                                         '</td><td class="tprod_qty">'+product['qty']+
                                         '</td><td class="tprod_hrs">'+product['hours']+
                                         '</td></tr>');
        });
    }
</script>

</body>
</html>