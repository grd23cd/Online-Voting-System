<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
body, table, th, td, h1, h3, .box {
  font-family: Times !important;
}

.content-wrapper {
  background-color: #F1E9D2 !important;
  color: black;
}

.box {
  background-color: #d8d1bd !important;
}

@media print {
  body * {
    visibility: hidden;
  }

  .print-section, .print-section * {
    visibility: visible;
  }

  .print-section {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }

  .no-print {
    display: none !important;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  tr {
    page-break-inside: avoid;
  }

  thead {
    display: table-header-group;
  }
}
</style>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">

<section class="content-header">
  <h1><b>Per Precinct</b></h1>
</section>

<section class="content">

<?php

$limit = 1;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$total_query = $conn->query("SELECT COUNT(DISTINCT precinct_number) as total FROM votes");
$total_row = $total_query->fetch_assoc();
$total_precincts = $total_row['total'];

$total_pages = ceil($total_precincts / $limit);
$offset = ($page - 1) * $limit;

$precinct_query = $conn->query("
  SELECT DISTINCT precinct_number
  FROM votes
  ORDER BY precinct_number ASC
  LIMIT $offset, $limit
");

?>

<div class="no-print" style="text-align:center; margin-bottom:15px;">

  <?php if($page > 1): ?>
    <a class="btn btn-default btn-sm" href="?page=<?php echo $page-1; ?>">Prev</a>
  <?php endif; ?>

  <?php for($i = 1; $i <= $total_pages; $i++): ?>
    <a class="btn btn-sm <?php echo ($i == $page) ? 'btn-primary' : 'btn-default'; ?>"
       href="?page=<?php echo $i; ?>">
      <?php echo $i; ?>
    </a>
  <?php endfor; ?>

  <?php if($page < $total_pages): ?>
    <a class="btn btn-default btn-sm" href="?page=<?php echo $page+1; ?>">Next</a>
  <?php endif; ?>

</div>

<?php while($p = $precinct_query->fetch_assoc()):
$precinct = $p['precinct_number'];
?>

<div class="box print-section" data-precinct="<?php echo $precinct; ?>">

  <div class="box-header with-border" style="display:flex; align-items:center;">

    <h3 style="margin:0;">Precinct <?php echo $precinct; ?></h3>

    <a href="javascript:void(0)"
      class="btn btn-success btn-sm btn-curve"
      style="margin-left:auto; background-color:#2E8B57;color:black;font-size:12px;font-family:Times"
      onclick="printSection(this)">
      <span class="glyphicon glyphicon-print"></span>
      Print
    </a>

  </div>

  <div class="box-body">

    <table class="table">
      <thead>
        <tr>
          <th>Voter</th>
          <th>Votes Cast</th>
        </tr>
      </thead>

      <tbody>
        <?php
        $sql = "
          SELECT
            CONCAT(voters.firstname, ' ', voters.lastname) AS voter,
            GROUP_CONCAT(
              CONCAT(
                positions.description,
                ' - ',
                candidates.firstname,
                ' ',
                candidates.lastname
              )
              ORDER BY positions.priority ASC
              SEPARATOR ', '
            ) AS votes_cast
          FROM votes
          LEFT JOIN positions ON positions.id = votes.position_id
          LEFT JOIN candidates ON candidates.id = votes.candidate_id
          LEFT JOIN voters ON voters.id = votes.voters_id
          WHERE votes.precinct_number = '$precinct'
          GROUP BY votes.voters_id
          ORDER BY voter ASC
        ";

        $query = $conn->query($sql);

        while($row = $query->fetch_assoc()){
          echo "
            <tr>
              <td>".$row['voter']."</td>
              <td>".$row['votes_cast']."</td>
            </tr>
          ";
        }
        ?>
      </tbody>
    </table>

  </div>
</div>

<?php endwhile; ?>

</section>
</div>

<?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function () {
  $('.table').each(function () {
    if ($.fn.DataTable.isDataTable(this)) {
      $(this).DataTable().destroy();
    }

    $(this).DataTable({
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      order: [[0, 'asc']]
    });
  });
});
</script>

<script>
function printSection(btn) {

  let section = btn.closest('.print-section');
  let precinct = section.getAttribute("data-precinct");

  fetch("fetch_votes_print.php?precinct=" + precinct)
    .then(res => res.json())
    .then(data => {

      let html = `
        <html>
          <head>
            <title>Precinct Report</title>
            <style>
              body { font-family: Times; padding:20px; }
              h3 { text-align:center; margin-bottom:15px; }
              table { width:100%; border-collapse: collapse; }
              table, th, td {
                border:1px solid black;
                padding:8px;
                vertical-align: top;
              }
              th {
                background:#d8d1bd;
              }
            </style>
          </head>
          <body>
            <h3>Precinct ${precinct}</h3>
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Voter</th>
                  <th>Votes Cast</th>
                </tr>
              </thead>
              <tbody>
      `;

      data.forEach((row, index) => {
        html += `
          <tr>
            <td>${index + 1}</td>
            <td>${row.voter}</td>
            <td>${row.votes_cast}</td>
          </tr>
        `;
      });

      html += `
              </tbody>
            </table>
          </body>
        </html>
      `;

      let iframe = document.createElement('iframe');
      iframe.style.position = 'fixed';
      iframe.style.right = '0';
      iframe.style.bottom = '0';
      iframe.style.width = '0';
      iframe.style.height = '0';
      iframe.style.border = '0';

      document.body.appendChild(iframe);

      iframe.contentDocument.open();
      iframe.contentDocument.write(html);
      iframe.contentDocument.close();

      iframe.onload = function () {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        document.body.removeChild(iframe);
      };

    });
}
</script>

</body>
</html>
