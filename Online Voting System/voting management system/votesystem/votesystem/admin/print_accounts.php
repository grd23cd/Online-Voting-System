<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper" style="background-color:#F1E9D2">

    <section class="content-header">
        <h1><b>Authorized User List</b></h1>

        <ol class="breadcrumb" style="color:black;font-size:17px;font-family:Times">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active" style="color:black;font-size:17px;font-family:Times">
                Dashboard
            </li>
        </ol>
    </section>

    <section class="content">

        <?php
        if(isset($_SESSION['error'])){
            echo "
            <div class='alert alert-danger alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert'>&times;</button>
                <h4><i class='icon fa fa-warning'></i> Error!</h4>
                ".$_SESSION['error']."
            </div>
            ";
            unset($_SESSION['error']);
        }

        if(isset($_SESSION['success'])){
            echo "
            <div class='alert alert-success alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert'>&times;</button>
                <h4><i class='icon fa fa-check'></i> Success!</h4>
                ".$_SESSION['success']."
            </div>
            ";
            unset($_SESSION['success']);
        }
        ?>

        <div class="row">
            <div class="col-xs-12">

                <div class="box" style="background-color:#d8d1bd">

                    <div class="box-header with-border" style="background-color:#d8d1bd">

                        <?php
                        $count = $conn->query("SELECT * FROM print_accounts")->num_rows;

                        if($count < 2){
                        ?>
                            <a href="#addnew"
                               data-toggle="modal"
                               class="btn btn-primary btn-sm btn-curve">
                                <i class="fa fa-plus"></i> New
                            </a>
                        <?php
                        }
                        ?>

                    </div>

                    <div class="box-body">

                        <table id="example1" class="table table-bordered table-striped">

                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Tools</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php

                                $sql = "SELECT * FROM print_accounts";
                                $query = $conn->query($sql);

                                while($row = $query->fetch_assoc()){

                                    echo "
                                    <tr>
                                        <td>".$row['fullname']."</td>
                                        <td>".$row['username']."</td>

                                        <td>

                                            <button class='btn btn-success btn-sm edit'
                                                data-id='".$row['id']."'>
                                                <i class='fa fa-edit'></i> Edit
                                            </button>

                                            <button class='btn btn-danger btn-sm delete'
                                                data-id='".$row['id']."'>
                                                <i class='fa fa-trash'></i> Delete
                                            </button>

                                        </td>

                                    </tr>
                                    ";

                                }

                                ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>
        </div>

    </section>

</div>

<?php include 'includes/footer.php'; ?>

<!-- Change this to your own modal file -->
<?php include 'includes/print_accounts_modal.php'; ?>

</div>

<?php include 'includes/scripts.php'; ?>

<script>

$(function(){

    $(document).on('click', '.edit', function(e){
        e.preventDefault();

        $('#edit').modal('show');

        var id = $(this).data('id');

        getRow(id);
    });


    $(document).on('click', '.delete', function(e){

        e.preventDefault();

        $('#delete').modal('show');

        var id = $(this).data('id');

        getRow(id);

    });

});


function getRow(id){

    $.ajax({

        type: 'POST',

        url: 'print_account_row.php',

        data: {id:id},

        dataType: 'json',

        success: function(response){

            $('.id').val(response.id);

            $('#edit_fullname').val(response.fullname);

            $('#edit_username').val(response.username);

            $('#edit_current_password').val('');
            $('#edit_password').val('');

            $('.fullname').html(response.fullname);

        }

    });

}

</script>

</body>
</html>