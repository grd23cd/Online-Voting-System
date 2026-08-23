<!-- Preview -->
<div class="modal fade" id="preview_modal">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #d8d1bd ;color:black ; font-size: 15px; font-family:Times" >
            <div class="modal-header">
              <button type="button"  class=" btn btn-close btn-curve pull-right" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" style="color:black ; font-size: 15px; font-family:Times">Vote Preview</h4>
            </div>
            <div class="modal-body">
              <div id="preview_body"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-curve pull-left" style='background-color:  #FFDEAD  ;color:black ; font-size: 12px; font-family:Times' data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Platform -->
<div class="modal fade" id="platform">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><span class="candidate"></b></h4>
            </div>
            <div class="modal-body">
              <p id="plat_view"></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- View Ballot -->
<div class="modal fade" id="view">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #d8d1bd ;color:black ; font-size: 15px; font-family:Times ">
            <div class="modal-header">
              <button type="button" class=" btn btn-close btn-curve pull-right" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"> <b>Your Votes </b></h4>
            </div>
            <div class="modal-body">
              <div class="votelist-wrapper">
              <?php
                $id = $voter['id'];
                $sql = "
                  SELECT 
                    positions.description,
                    CASE 
                      WHEN votes.candidate_id IS NULL THEN 'ABSTAINED'
                      ELSE CONCAT(candidates.firstname, ' ', candidates.lastname)
                    END AS candidate_name
                  FROM votes 
                  LEFT JOIN candidates ON candidates.id = votes.candidate_id 
                  LEFT JOIN positions ON positions.id = votes.position_id 
                  WHERE voters_id = '$id' 
                  ORDER BY positions.priority ASC
                ";
                $query = $conn->query($sql);
                while($row = $query->fetch_assoc()){
                  echo "
                    <div class='votelist'>
                      <span class='vote-label'>".$row['description']." :</span>
                      <span class='vote-value'>".$row['candidate_name']."</span>
                    </div>
                  ";
                }
              ?>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-curve pull-left" style='background-color:  #FFDEAD  ;color:black ; font-size: 12px; font-family:Times' data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<style>
/* View Ballot / your votes - flexbox layout instead of Bootstrap col-sm spans,
   which don't stack properly on mobile because they're inline <span> elements. */
.votelist-wrapper{
	display:flex;
	flex-direction:column;
	gap:6px;
}
.votelist{
	display:flex;
	flex-wrap:wrap;
	align-items:baseline;
	gap:6px 10px;
	padding:6px 0;
	border-bottom:1px solid rgba(0,0,0,0.15);
}
.votelist .vote-label{
	flex:1 1 160px;
	font-weight:bold;
	text-align:right;
}
.votelist .vote-value{
	flex:2 1 180px;
	text-align:left;
}

/* Stack label above value on narrow screens instead of squeezing side by side */
@media (max-width:480px){
	.votelist{
		flex-direction:column;
		gap:2px;
	}
	.votelist .vote-label,
	.votelist .vote-value{
		flex:1 1 auto;
		text-align:left;
	}
}

/* Let modal dialogs use available width comfortably on small screens */
@media (max-width:480px){
	#view .modal-dialog,
	#preview_modal .modal-dialog{
		width:auto;
		margin:10px;
	}
}
</style>