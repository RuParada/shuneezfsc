<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="myModalLabel">Edit City</h4>
</div>
{!! Form::open(array("url" => "admin/updatecity")) !!}
<div class="modal-body">
	<div class="form-group">
		<label>City</label>
		<input type="text" name="city" required class="form-control" placeholder="City" value="<?php echo $city->city; ?>">
	</div>        
	<div class="form-group">
		<label style="display:block">Status</label>
		<div class="radio_btns">
			<label> <input type="radio" name="status" value="1" <?php echo ($city->status == 1) ? 'checked' : ''; ?>> Active</label>
			<label> <input type="radio" name="status" value="0" <?php echo ($city->status == 0) ? 'checked' : ''; ?>> Inactive</label>
        </div>
	</div>                   
</div>

<div class="modal-footer forgot_btn">
	<input type="hidden" name="id" value="<?php echo $city->id; ?>">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<button type="submit" class="btn btn-primary">Update City</button>	
</div>

<script>
$(function () {        
    $(".selectLists").select2();
});
</script>

{!! Form::close() !!}
