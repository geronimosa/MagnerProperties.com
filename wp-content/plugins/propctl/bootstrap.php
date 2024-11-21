<!DOCTYPE html>
<html>

	<!-- Import bootstrap cdn -->
	<link rel="stylesheet" href=
"https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
		integrity=
"sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2"
		crossorigin="anonymous">

	<!-- Import jquery cdn -->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
		integrity=
"sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
		crossorigin="anonymous">
	</script>
	
	<script src=
"https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
		integrity=
"sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
		crossorigin="anonymous">
	</script>
 <!-- Modal -->
        <a href='#myModal' data-toggle='modal' id='3107811' data-target='#magnerProperty' >(3107811)</a>
        
        
        
                    <div id="magnerProperty" class="modal fade" aria-labelledby="MagnerLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content" style="width: 850px; height: 750px;">
                                <div class="modal-header" style="width: 100%; height: 50px;">
                                    <h5 id="MagnerLabel" class="modal-title">Property</h5>
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true"> × </span> </button>
                                </div>
                                <div class="modal-body"><!-- Data passed is displayed
                                                                                    in this part of the
                                                                                    modal body -->
                                    <h6 id="modal_body">a1 </h6>
                                    <h6 id="modal_text">a2 </h6>
                                    <button id="submit" class="btn btn-success btn-sm" type="button" data-toggle="modal" data-target="#magnerProperty"> Close </button>
                                </div>
                            </div>
                        </div>
                    </div>
        
        <script type="text/javascript">
                $("#magnerProperty").on("shown.bs.modal", function(e) {
                  var $relatedTarget = e.relatedTarget.id;
                  var str = "<iframe src='showlisting.php?id="
				 + $relatedTarget
				+ "'  style='width:800px; height:600px' ></iframe>" ;
                $("#modal_body").html(str);                        
                $("#modal_text").html($relatedTarget);
                });
                
	</script>