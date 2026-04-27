	<?php include 'includes/session.php'; ?>
	<?php include 'includes/header.php'; ?>
	<body class="hold-transition skin-blue layout-top-nav">
	<div class="wrapper">

		<?php include 'includes/navbar.php'; ?>
		
		<div class="content-wrapper" style="background-color: #F1E9D2 ">
			<div class="container" style="background-color: #F1E9D2 ">

			<!-- Main content -->
			<section class="content">
				<?php
					$parse = parse_ini_file('admin/config.ini', FALSE, INI_SCANNER_RAW);
					$title = $parse['election_title'];
				?>
				<h1 class="page-header text-center title"><b><?php echo strtoupper($title); ?></b></h1>
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<?php
							if(isset($_SESSION['error'])){
								?>
								<div class="alert alert-danger alert-dismissible">
									<button type="button" class="close" data-dismiss="alert">&times;</button>
									<ul>
										<?php
											foreach($_SESSION['error'] as $error){
												echo "<li>".$error."</li>";
											}
										?>
									</ul>
								</div>
								<?php
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

						<div class="alert alert-danger alert-dismissible" id="alert" style="display:none;">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<span class="message"></span>
						</div>

						<?php
							$sql = "SELECT voted FROM voters WHERE id = '".$voter['id']."'";
							$vquery = $conn->query($sql);
							$vrow = $vquery->fetch_assoc();

							if($vrow && $vrow['voted'] == 1){

								?>
								<div class="text-center" style="color:black; font-size:35px; font-family:Times">
									<h3>You have already voted for this election.</h3>
									<a href="#view" data-toggle="modal" class="btn btn-primary btn-lg" style="background-color:#4682B4;color:black;">View Ballot</a>
								</div>
								<?php
							}
							else{
								?>
								<form method="POST" id="ballotForm" action="submit_ballot.php">
									<?php
										include 'includes/slugify.php';

										$candidate = '';
										$sql = "SELECT * FROM positions ORDER BY priority ASC";
										$query = $conn->query($sql);

										while($row = $query->fetch_assoc()){
											$sql = "SELECT * FROM candidates WHERE position_id='".$row['id']."'";
											$cquery = $conn->query($sql);

											while($crow = $cquery->fetch_assoc()){
												$slug = slugify($row['description']);
												$checked = '';

												if(isset($_SESSION['post'][$slug])){
													$value = $_SESSION['post'][$slug];

													if(is_array($value)){
														foreach($value as $val){
															if($val == $crow['id']){
																$checked = 'checked';
															}
														}
													}
													else{
														if($value == $crow['id']){
															$checked = 'checked';
														}
													}
												}

												$input = ($row['max_vote'] > 1)
													? '<input type="checkbox" class="flat-red '.$slug.'" name="'.$slug.'[]" value="'.$crow['id'].'" '.$checked.'>'
													: '<input type="radio" class="flat-red '.$slug.'" name="'.$slug.'" value="'.$crow['id'].'" '.$checked.'>';

												$image = (!empty($crow['photo'])) ? 'images/'.$crow['photo'] : 'images/profile.jpg';

												$candidate .= '
												<br>
												<li class="candidate-item">
													'.$input.'
													<img src="'.$image.'" height="100" width="100" style="margin-left:10px;">
													<span style="margin-left:10px;">'.$crow['firstname'].' '.$crow['lastname'].'</span>

													<button type="button" 
														class="btn btn-primary btn-sm platform"
														style="margin-left:10px;background:#4682B4;color:black;"
														data-platform="'.$crow['platform'].'"
														data-fullname="'.$crow['firstname'].' '.$crow['lastname'].'">
														Platform
													</button>
												</li>';
											}

											$instruct = ($row['max_vote'] > 1)
												? 'You may select up to '.$row['max_vote'].' candidates'
												: 'Select only one candidate';

											echo '
											<div class="box box-solid" style="background:#d8d1bd">
												<div class="box-header">
													<h3>'.$row['description'].'</h3>
												</div>
												<div class="box-body">
													<p>'.$instruct.'
														<button type="button" class="btn btn-success btn-sm reset pull-right"
															data-desc="'.$slug.'">Reset</button>
													</p>
													<ul>'.$candidate.'</ul>
												</div>
											</div>';

											$candidate = '';
										}
									?>

									<div class="text-center">
										<button type="button" class="btn btn-success" id="preview">Preview</button>
										<button type="submit" class="btn btn-primary" name="vote">Submit</button>
									</div>
								</form>
								<?php
							}
						?>
					</div>
				</div>
			</section>
			</div>
		</div>

		<?php include 'includes/footer.php'; ?>
		<?php include 'includes/ballot_modal.php'; ?>
	</div>

	<?php include 'includes/scripts.php'; ?>

	<style>
	.candidate-item{
		cursor:pointer;
		padding:10px;
		border-radius:8px;
		margin-bottom:10px;
		list-style: none;
	}
	.candidate-item:hover{
		background:#eee;
	}
	.candidate-item.selected{
		background:#cce5ff;
		border:1px solid #007bff;
	}
	</style>

	<script>
	$(function(){

		$('.content').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		})
		.on('ifChecked', function(){
			var input = $(this);
			var item = input.closest('.candidate-item');

			if(input.attr('type') === 'radio'){
				var name = input.attr('name');
				$('input[name="'+name+'"]').closest('.candidate-item').removeClass('selected');
			}
			item.addClass('selected');
		})
		.on('ifUnchecked', function(){
			$(this).closest('.candidate-item').removeClass('selected');
		});

		// CLICK ROW
		$(document).on('click', '.candidate-item', function(e){
			if($(e.target).closest('.platform').length) return;

			var input = $(this).find('input');

			if(input.attr('type') === 'radio'){
				input.iCheck('check');
			}else{
				if(input.prop('checked')){
					input.iCheck('uncheck');
				}else{
					input.iCheck('check');
				}
			}
		});

		// RESET
		$(document).on('click', '.reset', function(e){
			e.preventDefault();
			var desc = $(this).data('desc');
			$('.'+desc).iCheck('uncheck');
		});

		// PLATFORM
		$(document).on('click', '.platform', function(e){
			e.preventDefault();
			$('#platform').modal('show');
			$('.candidate').html($(this).data('fullname'));
			$('#plat_view').html($(this).data('platform'));
		});

		// PREVIEW
		$('#preview').click(function(e){
			e.preventDefault();
			var form = $('#ballotForm').serialize();

			if(form == ''){
				$('.message').html('You must vote atleast one candidate');
				$('#alert').show();
			}
			else{
				$.ajax({
					type:'POST',
					url:'preview.php',
					data:form,
					dataType:'json',
					success:function(response){
						if(response.error){
							$('.message').html(response.message);
							$('#alert').show();
						}
						else{
							$('#preview_modal').modal('show');
							$('#preview_body').html(response.list);
						}
					}
				});
			}
		});
	});
	</script>
	</body>
	</html>