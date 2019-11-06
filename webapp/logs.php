<?php include 'include/header.php'; ?>
</head>
<body class='logs'>
<div class='container'>
    <div class='side-nav'>
        <?php include 'include/nav.php'; ?>
    </div>
    <div class='content'>
        <div class='content-header'>
            <h2>Logs</h2>
        </div>
        <div class="edit-links">
            <a class="modal-link" href="#openNewLogModal">New Log Entry</a>
            <div id="openNewLogModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header">New Log Entry</h2>
                    <div class="input-group"><label for="new-log-emp">Employee Id</label><input type="text" id="new-log-emp"  size="2"></div>
                    <div class="input-group"><label for="new-log-wrkstn">Workstation Id</label><input type="text" id="new-log-wrkstn" size="2"></div>
                    <div class="input-group"><label for="new-log-job">Job Id</label><input type="text" id="new-log-job" size="2"></div>
                    <div class="input-group"><label for="new-log-prod">Product Id</label><input type="text" id="new-log-prod" size="2"></div>
                    <div class="input-group"><label for="new-log-action">Action</label><input type="text" id="new-log-action" size="2"></div>
                    
                    <button type="button" id="new-log-btn">Submit</button>
                </div>
            </div>
        </div>
        <table class='db-table'>
            <col class="id-col">
	        <col class="date-col">
            <col class="emp-col">
            <col class="wrkstn-col">
            <col class="job-col">
            <col class="prod-col">
            <col class="action-col">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Workstation</th>
                    <th>Job</th>
                    <th>Product</th>
                    <th>Action</th>
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

    $('#new-log-btn').click(function() {
        var args = {
            'task': 11,
            'employee_id': $('#new-log-emp').val(),
            'workstation_id': $('#new-log-wrkstn').val(),
            'job_id': $('#new-log-job').val(),
            'product_id': $('#new-log-prod').val(),
            'action': $('#new-log-action').val()
        };

        api_call(args);
    });

    function reload_table_data() {
        //clear table body rows if any
        var table = $('.db-table').DataTable();
        table.clear();

        //load new content from database and fill table rows
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 10})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            data.forEach(function(el) {
                table.row.add($('<tr><td>'+el['id']+
                                            '</td><td>'+el['date_logged']+
                                            '</td><td>'+el['employee_id']+
                                            '</td><td>'+el['workstation_id']+
                                            '</td><td>'+el['job_id']+
                                            '</td><td>'+el['product_id']+
                                            '</td><td>'+el['action']+'</td></tr>')).draw();
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