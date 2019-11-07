<?php include 'include/header.php'; ?>
</head>
<body class='customers'>
<div class='container'>
    <div class='side-nav'>
        <?php include 'include/nav.php'; ?>
    </div>
    <div class='content'>
        <div class='content-header'>
            <h4>Customers</h4>
        </div>
        <div class="edit-links">
            <a class="modal-link" href="#openNewCustModal">New Customer</a>
            <div id="openNewCustModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header">New Customer</h2>
                    <div class="input-group"><label for="new-cust_id-text">Customer Id</label><input type="text" id="new-cust_id-text" size="2"></div>
                    <div class="input-group"><label for="new-name-text">Customer Name</label><input type="text" id="new-name-text"  size="20"></div>
                    <div class="input-group"><label for="new-notes-text">Notes</label><textarea id="new-notes-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" class="submit-btn" id="new-cust-btn">Submit</button>
                </div>
            </div>
            <div id="openEditCustModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header"></h2>
                    <div class="input-group"><label for="edit-cust_id-text">Customer Id</label><input type="text" id="edit-cust_id-text" size="2"></div>
                    <div class="input-group"><label for="edit-name-text">Customer Name</label><input type="text" id="edit-name-text"  size="20"></div>
                    <div class="input-group"><label for="edit-notes-text">Notes</label><textarea id="edit-notes-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" class="submit-btn" id="edit-cust-btn">Submit</button>
                    <button type="button" class="delete-btn" id="del-cust-btn">Delete Customer</button>
                </div>
            </div>
        </div>
        
        <table class='db-table'>
            <col class="tcust_id-col">
            <col class="tname-col">
            <col class="tnotes-col">
            <thead>
                <tr>
                    <th>Customer Id</th>
                    <th>Customer Name</th>
                    <th>Notes</th>
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
        $('.db-table').DataTable({
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
        reload_table_data();
    });

    $('#new-cust-btn').click(function() {
        var args = {
            'task': 41,
            'customer_id': $('#new-cust_id-text').val(),
            'name': $('#new-name-text').val(),
            'notes': $('#new-notes-text').val()
        };

        api_call(args);
        //reset text boxes
        $('#new-cust_id-text').text('');
        $('#new-name-text').text('');
        $('#new-notes-text').text('');
        //close modal
        window.location = '#close';
    });

    $('#edit-cust-btn').click(function() {
        var args = {
            'task': 42,
            'customer_id': $('#edit-cust_id-text').val(),
            'new_name': $('#edit-name-text').val(),
            'name': $('#openEditCustModal .modal-header').text(),
            'notes': $('#edit-notes-text').val()
        };

        api_call(args);
        //reset text boxes
        $('#edit-cust_id-text').text('');
        $('#edit-name-text').text('');
        $('#edit-notes-text').text('');
        //close modal
        window.location = '#close';
    });

    $('#del-cust-btn').click(function() {
        var args = {'task': 43,
                    'name': $('#openEditCustModal .modal-header').text()
                   };

        api_call(args);
        window.location = '#close';
    });

    $(document).on('click', '.db-table tr', function() {
        window.location = '#openEditCustModal';
        //set header to current name of employee
        $('#openEditCustModal .modal-header').text($(this).find('td:eq(1)').text());
        //fill input with data for employee that was clicked on
        $('#edit-cust_id-text').val($(this).find('td:eq(0)').text());
        $('#edit-name-text').val($(this).find('td:eq(1)').text());
        $('#edit-notes-text').val($(this).find('td:eq(2)').text());
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
        });
    }

    function reload_table_data() {
        //clear table body rows if any
        var table = $('.db-table').DataTable();
        table.clear();

        //load new content from database and fill table rows
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify({'task': 40})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            data.forEach(function(el) {
                table.row.add($('<tr><td>'+el['customer_id']+
                                            '</td><td>'+el['name']+
                                            '</td><td>'+el['notes']+'</td></tr>')).draw();
            });
            
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }
</script>