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
        </div>

        <div id="openProductModal" class="modal-dialog">
            <div>
                <a href="#close" title="Close" class="close">X</a>
                <h2 class="modal-header"></h2>

            </div>
        </div>
        <div class='footer'>
            <?php include "include/footer.php"; ?>
        </div>
    </div>

<script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
<script src='js/helpers.js'></script>

<script type='text/javascript'>
    $(document).ready(function() {
        reload_content();

    });

    $(document).on('click', '.prod-modal-link', function() {
        var job_id = $(this).parents('.product-detail').siblings('.job-title').text();
        var product_id = $(this).text().split('-')[0];
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 15,
                                  'job_id': job_id,
                                  'product_id':product_id})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            console.log(data);
        });
    });

    function gen_product_modal(job_id, product_name, product_id, qty) {
        var $modal = $('<div id="'+product_id+'Modal" class="modal-dialog">');

        var $content = $('<div></div>');
        $('<a href="#close" title="Close" class="close">X</a>').appendTo($content);
        $('<h2 class="modal-header">'+product_id+' - '+product_name+' ('+qty+')</h2>').appendTo($content);

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
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }

    function gen_job_detail(job) {
        var status = get_job_status(job[0]['date_started'],job[0]['date_finished']);
        var $job_detail = $('<div class="job-detail"></div>');
        var $job_detail_header = $('<div class="job-detail-header"></div>');
        $('<h3 class="job-title">Job: '+job[0]['job_id']+'</h3>').appendTo($job_detail_header);
        $('<h3 class="job-status">Status: '+status+'</h3>').appendTo($job_detail_header);
        $('<h3 class="customer">Customer: '+job[0]['customer_name']+'</h3>').appendTo($job_detail_header);
        $job_detail_header.appendTo($job_detail);

        var $col1 = $('<div class="product-detail col-1"></div>');
        $('<p class="product-header">Product</p>').appendTo($col1);
        var $col2 = $('<div class="product-detail col-2"></div>');
        $('<p class="qty-header">Quantity</p>').appendTo($col2);
        var $col3 = $('<div class="product-detail col-3"></div>');
        $('<p class="hrs-header">Hours Worked</p>').appendTo($col3);
        $.each(job, function(i, product) {
            $('<p class="product col-1"><a class="prod-modal-link">'+
                product['product_id']+'-'+product['product_name']+'</a></p>').appendTo($col1);

            $('<p class="qty col-2"><a class="prod-modal-link">'+
                product['qty']+'</a></p>').appendTo($col2);

            $('<p class="product col-3"><a class="prod-modal-link">'+
                product['hours']+'</a></p>').appendTo($col3);
        });

        $col1.appendTo($job_detail);
        $col2.appendTo($job_detail);
        $col3.appendTo($job_detail);

        $('.content').append($job_detail);
    }
</script>

</body>
</html>