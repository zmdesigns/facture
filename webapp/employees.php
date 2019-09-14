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
        <button id='new-row-btn' type='button'>New Employee</button>
        <button id='rm-row-btn' type='button'>Delete Employee</button>
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
    });
</script>