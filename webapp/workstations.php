<?php include 'include/header.php'; ?>
</head>
<body>
<div class='container'>
    <div class='g-header'>
        <?php include 'include/nav.php'; ?>
        <h1>Workstations</h1>
    </div>
    <div class='g-table'>
        <div class="edit-links">
            <a class="modal-link" href="#openNewWrkModal">New Workstation</a>
            <div id="openNewWrkModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header">New Workstation</h2>
                    <div class="input-group"><label for="new-name-text">Workstation Name</label><input type="text" id="new-name-text"  size="20"></div>
                    <div class="input-group"><label for="new-id-text">Workstation Id</label><input type="text" id="new-id-text" size="2"></div>
                    <div class="input-group"><label for="new-notes-text">Notes</label><textarea id="new-notes-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" id="new-wrk-btn">Submit</button>
                </div>
            </div>
            <div id="openEditWrkModal" class="modal-dialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="modal-header"></h2>
                    <div class="input-group"><label for="edit-name-text">Workstation Name</label><input type="text" id="edit-name-text"  size="20"></div>
                    <div class="input-group"><label for="edit-id-text">Workstation Id</label><input type="text" id="edit-id-text" size="2"></div>
                    <div class="input-group"><label for="edit-notes-text">Notes</label><textarea id="edit-notes-text" rows="3" columns="7"></textarea></div>
                    
                    <button type="button" id="edit-wrk-btn">Submit</button>
                    <button type="button" id="del-wrk-btn">Delete Workstation</button>
                </div>
            </div>
        </div>
        
        <table class='db-table'>
            <col class="tname-col">
	        <col class="tid-col">
            <col class="tnotes-col">
            <thead>
                <tr>
                    <th>Workstation Name</th>
                    <th>Workstation Id</th>
                    <th>Activity</th>
                    <th>Description</th>
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

    $('#new-wrk-btn').click(function() {
        var args = {
            'task': 51,
            'name': $('#new-name-text').val(),
            'station_id': $('#new-id-text').val(),
            'notes': $('#new-notes-text').val()
        };

        api_call(args);
        //reset text boxes
        $('#new-name-text').text('');
        $('#new-id-text').text('');
        $('#new-notes-text').text('');
        //close modal
        window.location = '#close';
    });

    $('#edit-wrk-btn').click(function() {
        var args = {
            'task': 52,
            'new_name': $('#edit-name-text').val(),
            'name': $('#openEditWrkModal .modal-header').text(),
            'station_id': $('#edit-id-text').val(),
            'notes': $('#edit-notes-text').val()
        };

        api_call(args);
        //reset text boxes
        $('#edit-name-text').text('');
        $('#edit-id-text').text('');
        $('#edit-notes-text').text('');
        //close modal
        window.location = '#close';
    });

    $('#del-wrk-btn').click(function() {
        var args = {'task': 53,
                    'name': $('#openEditWrkModal .modal-header').text()
                   };

        api_call(args);
        window.location = '#close';
    });

    $(document).on('click', '.db-table tr', function() {
        window.location = '#openEditWrkModal';
        //set header to current name of employee
        $('#openEditWrkModal .modal-header').text($(this).find('td:eq(0)').text());
        //fill input with data for employee that was clicked on
        $('#edit-name-text').val($(this).find('td:eq(0)').text());
        $('#edit-id-text').val($(this).find('td:eq(1)').text());
        $('#edit-notes-text').val($(this).find('td:eq(3)').text());
    });

    function api_call(args,reload=true) {
        data = fetch('include/api.php', {
            method: 'POST',
            body: JSON.stringify(args)
        }).then(response => response.text())
        .then(function(data) {
            console.log(data); //todo: if page returns an error, let the user know
            if (reload) {
                reload_table_data();
            }
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
            body: JSON.stringify({'task': 50})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            $.each(data, function(index, el) {
                $('.db-table tbody').append('<tr><td>'+el['name']+
                                            '</td><td>'+el['station_id']+
                                            '</td><td>'+
                                            '</td><td>'+el['notes']+'</td></tr>');

                var args = {'task': 14,
                            'workstation_id': el['station_id'],
                            'employee_id': '',
                            'job_id': ''};
                data = fetch('include/api.php', {
                    method: 'POST',
                    body: JSON.stringify(args)
                }).then(response => response.text())
                .then(function(data) {
                    $("td:contains('"+el['station_id']+"')").next().text(data);
                });
                
                
            });
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }
</script>