@extends('adminheader')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<?php $array_valid = (Session::has('array_valid')) ? Session::get('array_valid') : []; ?>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Update Branch Work Time'); ?> </h1>
		@if(Session::has('success'))<p class="success_msg"><?php echo Session::get('success'); ?></p>@endif
		@if(Session::has('error')) <p class="error_msg"> <?php echo trans('messages.Required'); ?></p> @endif
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="box">                
            <div class="box-body">
		{!! Form::open(array('url' => 'admin/update_branch_workingtime', 'class' => 'form-horizontal', 'id' => 'time_form')) !!}
			
		<div class="col-md-8">
		<h1><?php echo trans('messages.Working Time').' - '.$branch->branch; ?></h1>
		<?php if(count($workingtimes)) { ?>
		<table class="table table-bordered table-striped">
			<thead>
				<th><?php echo trans('messages.Days'); ?></th>
				<th><?php echo trans('messages.Open Time'); ?></th>
				<th><?php echo trans('messages.Close Time'); ?></th>
				<th><?php echo trans('messages.Action'); ?></th>
			</thead>
			<tbody>
				<?php foreach($workingtimes as $workingtime) { ?>
				<tr>
					<td><?php echo $workingtime->working_day; ?></td>
					<td><?php echo date('g:i A', strtotime($workingtime->start_time)); ?></td>
					<td><?php echo date('g:i A', strtotime($workingtime->close_time)); ?></td>
					<td width="30">
						<a href="#" class="delete_btn" onclick="delete_timeslot(<?php echo $workingtime->id; ?>);"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php } ?>
		<div class="box-body">
         	<div class="daylist">
                <div class="form-group col-md-12">
                    <label for="day" class="col-sm-4 control-label"><?php echo trans('messages.Select Working Day'); ?> <span class="req">*</span></label>
                    <div class="col-sm-8 full_selectLists">
	                    <select class="selectLists working_day" name="day">
							<option value=""></option>
							<?php
							$days = getdays(); 
							if(count($days) > 0) 
							{ 
								$i = 1;
								foreach($days as $key => $day) {
							?>
							<option value="<?php echo $key.'|'.$i; ?>"><?php echo $day; ?></option>
							<?php $i++; } } ?>
						</select>
                        <span class="error_msg day_error"></span>
                    </div>
                </div>
                
                <div class="form-group col-md-12 working_time">
                    <label for="last_name" class="col-sm-4 control-label"><?php echo trans('messages.Working Time'); ?> <span class="req">*</span></label>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-4 bootstrap-timepicker">
                            	<input type="text" placeholder="<?php echo trans('messages.Open Time'); ?>" class="timepicker start_time form-control" name="start_time[]" value="<?php echo date('g:i A', strtotime('00:00')); ?>" data-default="00:00">
                            </div>
                            <div class="col-sm-4 bootstrap-timepicker">
                                <input type="text" placeholder="<?php echo trans('messages.Close Time'); ?>" class="timepicker close_time form-control" name="close_time[]" value="" data-default="00:00">
                            </div>
                            <div class="col-sm-2 text-center actiontype">
                                <button type="button" class="plus_minus add"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                         <span class="error_msg time_error"></span>
                    </div>
                </div>
             </div>
            </div>
      </div>
	
	<div class="box-footer">
		<input type="hidden" name="branch_id" value="<?php echo $branch->id; ?>">
		<button type="button" id="savetime" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Work Time'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
		<button type="button" onclick="window.location.href='{!! URL::to('admin/branches') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
    </div>
	{!! Form::close() !!}	
	</div> <!-- box -->
	</div>	
    </section><!-- content -->

</div><!-- content-wrapper -->
<script type="text/javascript">

$(document).ready(function()
{
	$(document).on("click", ".add", function(){
		$clone = $(".working_time:last").clone();
		
		if(!$clone.find(".remove").length)
			$clone.find(".row").append('<div class="col-sm-2 text-center actiontype"><button type="button" class="plus_minus remove"><i class="fa fa-minus"></i></button></div>');
		else
			$(".working_time:last").find(".remove").parent().remove();
		
		$(".actiontype i").attr('class','fa fa-minus');
		$(".actiontype button").attr('class','plus_minus remove');
		$(".daylist").append($clone);
		$(".working_time:last label").html("");
		$(".error_msg:last").html("");
		$(".timepicker").timepicker({
						showInputs: false,
					    defaultTime: '',
					    minuteStep: 1,
					    maxHours : 24,
    					showMeridian : false
					});
		
	});
	
	$(document).on("click", ".remove", function(){
		$length = $(".working_time").length;
		
		$(this).parents(".working_time").slideUp(function(){ 
			$(this).remove(); 

			//$current = $(this).parents(".working_time:last");
			if(!$(".working_time:last").find(".add").length)
				$(".working_time:last").find(".row").append('<div class="col-sm-2 text-center actiontype"><button type="button" class="plus_minus add"><i class="fa fa-plus"></i></button></div>');
			if($(".working_time").length == 1)
				$(".working_time").find(".remove").parent().remove();
		});
		
		
		//.
			//console.log($(".working_time:eq(" + ($length - 1) + ")").find('input[name=\"start_time[]\"]').val());
		
		return false;
	});

});

</script>
<script>

/*
*/

function getHour24(timeString)
{
    time = null;
    var matches = timeString.match(/^(\d{1,2}):(\d{00,59}) (\w{2})/);
    if (matches != null)
    {
    	time = parseInt(matches[1]);

        if (matches[2] == 'PM')
        {
            time += 12;
        }
    }
    time = (time == 12) ? 0 : time;
    time = (time == 24) ? 12 : time;
    return time;
}

$(document).on("blur", ".close_time", function(){
	
	$parent  = $(this).parents(".working_time");
	$start_time = $($parent).find(".start_time").va();
	$close_time = $($parent).find(".close_time").va();

	$($parent).find("span.time_error").html('');
	
	if($start_time >= $close_time)
	{
		$($parent).find("span.time_error").html('Close time must be greater than start time');
	}
});

$(document).on("blur", ".start_time", function(){
	
	$parent  = $(this).parents(".working_time");
	$start_time = $($parent).find(".start_time").va();
	$close_time = $($parent).find(".close_time").va();
	$($parent).find("span.time_error").html('');
	
	if($start_time >= $close_time)
	{
		$($parent).find("span.time_error").html('Start time must be smaller than start time');
	}
});

$(document).on("click", "#savetime", function(e){
	$.ajaxSetup({
	        header:$('meta[name="_token"]').attr('content')
	    })
	e.preventDefault(e);
	$('span').removeClass("has_error");
	$('span.time_error').html("");
	$('span.day_error').html("");
	$(".working_time").each(function()
	{
		var day = $('.working_day').val();
		var start_time = $(this).find('.start_time').val();
	var close_time = $(this).find('.close_time').val();
	if(day == '')
		{
			$("span.day_error").html('Working day cannot be empty');
			$('span.day_error').addClass("has_error");
		}
		if(start_time >= close_time)
		{
			$(this).find("span.time_error").html('Close time must be greater than start time');
			$(this).find('span.time_error').addClass("has_error");
		}
	})
	
		if($(".has_error").length) {       
          return false;
        }
        else
        {
        	$("#time_form").submit();	
	    }
	})
	function delete_timeslot(id)
    {
    	var url = "<?php echo URL::to(''); ?>";
        if (confirm("<?php echo trans('messages.Delete Confirmation'); ?>"))
        {
            window.location = url+'/admin/delete_timeslot/' + id;
        }
    }
</script>

@endsection     
