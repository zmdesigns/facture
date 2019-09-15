<?php include 'include/header.php'; ?>
<link rel="stylesheet" href="css/employees.css">
</head>
<body>
<div class='container'>
    <div class='g-header'>
        <?php include 'include/nav.php'; ?>
        <h1>Employees</h1>
    </div>
    <div class='g-table'>
        <div class="emp-edit">
            <a class="modal-link" href="#openNewEmpModal">New Employee</a>
            <div id="openNewEmpModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header">New Employee</h2>
                    <div class="input-group"><label for="new-name-text">Employee Name</label><input type="text" id="new-name-text"  size="20"></div>
                    <div class="input-group"><label for="new-login-text">Login Code(4 Digits)</label><input type="text" id="new-login-text" size="2"></div>
                    <div class="input-group"><label for="new-notes-text">Notes</label><textarea id="new-notes-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" id="new-emp-btn">Submit</button>
                </div>
            </div>
            <div id="openEditEmpModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header"></h2>
                    <div class="input-group"><label for="edit-name-text">Employee Name</label><input type="text" id="edit-name-text"  size="20"></div>
                    <div class="input-group"><label for="edit-login-text">Login Code(4 Digits)</label><input type="text" id="edit-login-text" size="2"></div>
                    <div class="input-group"><label for="edit-notes-text">Notes</label><textarea id="edit-notes-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" id="edit-emp-btn">Submit</button>
                    <button type="button" id="del-emp-btn">Delete Employee</button>
                </div>
            </div>
        </div>
        
        <table class='db-table'>
            <col class="tname-col">
	        <col class="tlogin-col">
            <col class="tnotes-col">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Login Code</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        
    </div>
    <div class='g-footer'>
        <?php include 'include/footer.php'; ?>
    </div>
</div>
</body>


<script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>

<script type='text/javascript'>
	$(document).ready(function() {
        reload_table_data();
    });

    $('#new-emp-btn').click(function() {
        var args = {
            'task': 'new',
            'name': $('#new-name-text').val(),
            'login': $('#new-login-text').val(),
            'notes': $('#new-notes-text').val()
        };

        api_call(args);
        //reset text boxes
        $('#new-name-text').text('');
        $('#new-login-text').text('');
        $('#new-notes-text').text('');
        //close modal
        window.location = '#close';
        reload_table_data();
    });

    $('#edit-emp-btn').click(function() {
        var args = {
            'task': 'edit',
            'new_name': $('#edit-name-text').val(),
            'old_name': $('#openEditEmpModal .modal-header').text(),
            'login': $('#edit-login-text').val(),
            'notes': $('#edit-notes-text').val()
        };

        api_call(args);
        //reset text boxes
        $('#edit-name-text').text('');
        $('#edit-login-text').text('');
        $('#edit-notes-text').text('');
        //close modal
        window.location = '#close';
        reload_table_data();
    });

    $('#del-emp-btn').click(function() {
        var args = {'task': 'delete',
                    'name': $('#openEditEmpModal .modal-header').text()
                   };

        api_call(args);
        window.location = '#close';
        reload_table_data();
    });

    $(document).on('click', '.db-table tr', function() {
        window.location = '#openEditEmpModal';
        //set header to current name of employee
        $('#openEditEmpModal .modal-header').text($(this).find('td:eq(0)').text());
        //fill input with data for employee that was clicked on
        $('#edit-name-text').val($(this).find('td:eq(0)').text());
        $('#edit-login-text').val($(this).find('td:eq(1)').text());
        $('#edit-notes-text').val($(this).find('td:eq(2)').text());
    });

    function api_call(args) {
        data = fetch('include/employee.php', {
            method: 'POST',
            body: JSON.stringify(args)
        }).then(function(data) {
            console.log(data); //todo: if page returns an error, let the user know
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }

    function reload_table_data() {
        //clear table body rows if any
        $('.db-table tbody').html('');

        //load new content from database and fill table rows
        data = fetch('include/employee.php', {
            method: 'POST',
            body: JSON.stringify({'task': 'list_all'})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            data.forEach(function(el) {
                $('.db-table tbody').append('<tr><td>'+el['name']+
                                            '</td><td>'+el['login']+
                                            '</td><td>'+el['notes']+'</td></tr>');
            });
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }
</script>