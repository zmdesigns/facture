<?php include 'include/header.php'; ?>
<link rel="stylesheet" href="css/products.css">
</head>
<body>
<div class='container'>
    <div class='g-header'>
        <?php include 'include/nav.php'; ?>
        <h1>Products</h1>
    </div>
    <div class='g-table'>
        <button id='new-row-btn' type='button'>New Product</button>
        <button id='rm-row-btn' type='button'>Delete Product</button>
        <table class='db-table'>
            <col class="tname-col">
	        <col class="tdescrip-col">
            <col class="tbtn-col">
            <col class="trem-col">
            <thead>
                <tr>
                    <th>Product name</th>
                    <th>Description</th>
                    <th></th>
                    <th></th>
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

    $('#new-row-btn').click(function() {
        var row_count = $('.db-table tr').length; //not+1 for the new row because it is zero indexed. used to identify input fields
        $('.db-table tbody').append('<tr><td><input id="name-input-'+row_count+'" type="text"></td>'+
                                     '<td><input id="description-input-'+row_count+'" type="text"></td>'+
                                     '<td><button id="'+row_count+'" class="save-btn new">Create</button></td></tr>');
    });

    $(document).on('click', '.edit-btn', function() { 
        //get data about the row
        var row = $(this).attr('id');
        var name_col = $('.db-table tr:eq('+row+') td:eq(0)');
        var description_col = $('.db-table tr:eq('+row+') td:eq(1)');
        var name = name_col.text();
        var description = description_col.text();
        
        //replace text with input fields
        name_col.html('<input id="name-input-'+row+'" type="text">');
        description_col.html('<input id="description-input-'+row+'" type="text">');
        //save old name in order to identify db entry if name is edited
        name_col.append('<p id="old-name-'+row+'" hidden>'+name+'</p>');

        //fill input fields with text data
        $('#name-input-'+row).val(name);
        $('#description-input-'+row).val(description);

        //adjust button so changes can be saved once pressed
        $(this).text('Save');
        $(this).addClass('save-btn');
        $(this).removeClass('edit-btn');
    });

    //for buttons that intend to make changes to database
    //ie-new product button, save button after clicking edit
    $(document).on('click', '.save-btn', function() {
        //get data about the row
        var row = $(this).attr('id');
        var name = $('#name-input-'+row).val();
        var description = $('#description-input-'+row).val();
        var name_col = $('.db-table tr:eq('+row+') td:eq(0)');
        var description_col = $('.db-table tr:eq('+row+') td:eq(1)');

        //do some special work if it is edit vs new button
        if ($(this).hasClass('edit')) {
            var old_name = $('#old-name-'+row).text();

            if (!edit_product(old_name, name, description)) {
                return false;
            }
        }
        else if ($(this).hasClass('new')) {
            if (new_product(name, description)) {      
                $(this).removeClass('new');
            }
            else {
                return false;
            }
        }
        else {
            console.log('unrecognized button class');
            return false;
        }

        //replace inputs with text
        name_col.html($('#name-input-'+row).val());
        description_col.html($('#description-input-'+row).val());
        //adjust button to pre-edit state
        $(this).addClass('edit-btn');
        $(this).removeClass('save-btn');
        $(this).text('Edit');
    });

    $(document).on('change', '.rm-box', function() {
        var row_obj = $('.db-table tr:eq('+this.value+')');
        if (this.checked) {
            row_obj.css('background-color','green');
        }
        else {
            row_obj.css('background-color','initial');
        }
    });

    $('#rm-row-btn').click(function() {
        $('.rm-box:checked').each(function(i,el) {
            var row = el.value;
            delete_product(row);
        });
    });

    function delete_product(row) {
        var name = $('.db-table tr:eq('+row+') td:eq(0)').text();
        var args = {'task': 'delete',
                    'name': name
                   };

        return api_call(args);
    }


    function edit_product(old_name, new_name, description) {
        var args = {'task': 'edit',
                    'name': old_name,
                    'new_name': new_name,
                    'description': description
                    };

        return api_call(args);
    }

    function new_product(name, description) {
        var args = {'task': 'new',
                    'prod_name': name,
                    'description': description
                    };
        
        return api_call(args);
    }

    function api_call(args) {
        data = fetch('include/product.php', {
            method: 'POST',
            body: JSON.stringify(args)
        }).then(function(data) {
            console.log(data); //todo: if page returns an error, let the user know
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
            return false;
        });
        return true;
    }

    function reload_table_data() {
        //clear table body rows if any
        $('.db-table tbody').html('');

        data = fetch('include/product.php', {
            method: 'POST',
            body: JSON.stringify({'task': 'list_all'})
        }).then(response => response.json()) // parses JSON response into native Javascript objects
        .then(function(data) {
            var row = 1; //starts at 1 because header row is 0
            data.forEach(function(el) {
                $('.db-table tbody').append('<tr><td>'+el['name']+
                                  '</td><td>'+el['description']+
                                  '</td><td><button id="'+row+'" class="edit-btn edit" type="button">Edit</button>'+
                                  '</td><td><input type="checkbox" class="rm-box" value="'+row+'"></td></tr>');
                row += 1;
            });
        }).catch(function(error) {
            console.log('There has been a problem with your fetch operation: ', error.message);
        });
    }

</script>