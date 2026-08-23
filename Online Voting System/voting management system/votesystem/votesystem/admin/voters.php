<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="background-color:#F1E9D2 ">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><b>
        Voters List
     </b> </h1>
      <ol class="breadcrumb" style="color:black ; font-size: 17px; font-family:Times">
        <li><a href="#"><i class="fa fa-dashboard" ></i> Home</a></li>
        <li class="active" style="color:black ; font-size: 17px; font-family:Times" >Dashboard</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12" >
          <div class="box" style="background-color: #d8d1bd">
            <div class="box-header with-border" style="background-color: #d8d1bd">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-curve " style="background-color: #4682B4 ;color:black ; font-size: 12px; font-family:Times"><i class="fa fa-plus"></i> New</a>
              <button type="button" id="btnPrintAll" class="btn btn-primary btn-sm btn-curve " style="background-color: #4682B4 ;color:black ; font-size: 12px; font-family:Times"><i class="fa fa-print"></i> Print</button>
            </div>
            <div class="box-body">
              <table id="example1" class="table ">
                <thead>
                  <th>Lastname</th>
                  <th>Firstname</th>
                  <th>Photo</th>
                  <th>Voters ID</th>
                  <th>Passbook Number</th>
                  <th>Unique Code</th>
                  <th>Tools</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM voters";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      $image = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';
                      echo "
                        <tr style='color:black ; font-size: 15px; font-family:Times'>
                          <td>".$row['lastname']."</td>
                          <td>".$row['firstname']."</td>
                          <td>
                            <img src='".$image."' width='30px' height='30px'>
                            <a href='#edit_photo' data-toggle='modal' class='pull-right photo' data-id='".$row['id']."'><span class='fa fa-edit'></span></a>
                          </td>
                          <td>".$row['voters_id']."</td>
                          <td>".$row['password']."</td>
                          <td>".$row['code']."</td>
                          <td>
                           
                            <button class='btn btn-success btn-sm edit btn-curve' style='background-color: #9CD095 ;color:black ; font-size: 12px; font-family:Times' ' data-id='".$row['id']."' ><i class='fa fa-edit'></i> Edit</button>
                            <button class='btn btn-danger btn-sm delete btn-curve' style='background-color:#ff8e88 ;color:black ; font-size: 12px; font-family:Times ' data-id='".$row['id']."'><i class='fa fa-trash'></i> Delete</button>

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
  <?php include 'includes/voters_modal.php'; ?>
</div>

<!-- ===================== PRINTABLE SECTION (hidden on screen, shown only when printing) ===================== -->
<div id="printArea" class="print-only">
  <div class="print-header">
    <h2>Voters List</h2>
    <p>Fullname, Voters ID, Passbook Number &amp; Unique Code</p>
  </div>
  <table class="print-table">
    <thead>
      <tr>
        <th>Fullname</th>
        <th>Voters ID</th>
        <th>Passbook Number</th>
        <th>Unique Code</th>
      </tr>
    </thead>
    <tbody>
      <?php
        // Re-run the query fresh so the print list always reflects the current database
        $printQuery = $conn->query("SELECT * FROM voters ORDER BY lastname, firstname");
        while($prow = $printQuery->fetch_assoc()){
          $fullname = htmlspecialchars($prow['firstname'].' '.$prow['lastname']);
          echo "
            <tr>
              <td>{$fullname}</td>
              <td>".htmlspecialchars($prow['voters_id'])."</td>
              <td>".htmlspecialchars($prow['password'])."</td>
              <td>".htmlspecialchars($prow['code'])."</td>
            </tr>
          ";
        }
      ?>
    </tbody>
  </table>
</div>

<style>
  /* Printable table is hidden on screen at all times */
  .print-only { display: none; }

  @media print {
    /* Hide everything in the normal page ... */
    body.printing-mode > .wrapper,
    body.printing-mode > .wrapper * {
      display: none !important;
    }

    /* ...and show only the printable table */
    body.printing-mode .print-only {
      display: block !important;
    }

    /* Auto-adjust to whatever paper size the printer/browser is set to,
       instead of hardcoding A4 or Letter */
    @page {
      size: auto;
      margin: 12mm;
    }

    #printArea {
      font-family: Times, "Times New Roman", serif;
      color: #000;
    }

    .print-header {
      text-align: center;
      margin-bottom: 10px;
    }

    .print-header h2 { margin: 0; font-size: 20px; }
    .print-header p { margin: 2px 0 0 0; font-size: 12px; color: #444; }

    .print-table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed; /* keeps proportions consistent on any paper size */
    }

    .print-table th, .print-table td {
      border: 1px solid #000;
      padding: 6px 8px;
      font-size: 12px;
      word-wrap: break-word;
      text-align: left;
    }

    .print-table th {
      background-color: #d8d1bd !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .print-table th:nth-child(1), .print-table td:nth-child(1) { width: 40%; }
    .print-table th:nth-child(2), .print-table td:nth-child(2) { width: 20%; }
    .print-table th:nth-child(3), .print-table td:nth-child(3) { width: 20%; }
    .print-table th:nth-child(4), .print-table td:nth-child(4) { width: 20%; }
  }
</style>
<!-- ===================== END PRINTABLE SECTION ===================== -->

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

  $(document).on('click', '.photo', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '#btnPrintAll', function(e){
    e.preventDefault();
    document.body.classList.add('printing-mode');
    window.print();
  });

  // Prevent double-submit (fast double-click / accidental resubmit) on Add Voter form
  $(document).on('submit', '#addVoterForm', function(){
    var $submitBtn = $(this).find('button[name="add"]');
    if ($submitBtn.prop('disabled')) {
      return false; // block any resubmit if somehow already disabled
    }
    $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
  });

  // Restore the normal page once the print dialog closes
  window.onafterprint = function(){
    document.body.classList.remove('printing-mode');
  };

});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'voters_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.id').val(response.id);
      $('#edit_firstname').val(response.firstname);
      $('#edit_lastname').val(response.lastname);
      $('#edit_password').val(response.password);
      $('#edit_code').val(response.code);
      $('.fullname').html(response.firstname+' '+response.lastname);
    }
  });
}
</script>
</body>
</html>