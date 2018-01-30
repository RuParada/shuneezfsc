@extends('adminheader')

@section('content')

@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Send Promocode'); ?> </h1>
        @if(Session::has('error')) <p class="error_msg"> Required(*) fields are missing</p> @endif
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Promocode details'); ?></h3>
                    </div><!--box-header-->
					<div class="box-body">
						<div class="form-group">
                            <label for="first_name" class="col-sm-3 control-label"><?php echo trans('messages.Promocode'); ?> :</label>
                            <div class="col-sm-9"> <p><?php echo $promocode->promocode; ?></p></div>
                        </div>
                     </div>
                     <div class="box-body">
                        <div class="form-group">
                            <label for="first_name" class="col-sm-3 control-label"><?php echo trans('messages.Expiry Date'); ?> :</label>
                            <div class="col-sm-9"> <?php echo date('d-M-y', strtotime($promocode->expiry_date)); ?></div>
                        </div>
                      </div>
                      <div class="box-body">
                        <div class="form-group">
                            <label for="first_name" class="col-sm-3 control-label"><?php echo trans('messages.Value'); ?> :</label>
                            <div class="col-sm-9"> <?php echo $promocode->amount; ?></div>
                        </div>
                        </div>
                        
                        {!! Form::open(array('url' => 'admin/mailpromocode', 'class' => 'form-horizontal', 'id' => 'send_form')) !!}
                        <div class="box-body">
                        <div class="form-group">
                            <div class="col-sm-4">
								<select multiple id="customer_selection" class="selection_boxes col-sm-12">
									<option value="All">All</option>
									<?php
									if(count($users)) { 
									foreach($users as $user) { ?>
										<option value="<?php echo $user->email; ?>"><?php echo  $user->first_name.' '.$user->last_name; ?></option>
									<?php } } ?>
								</select>
							</div>
							<div class="col-sm-2 selection_buttons">
								<button type="button" id="select" title="Select"><i class="fa fa-chevron-right"></i></button>
								<button type="button" id="deselect" title="Deselect"><i class="fa fa-chevron-left"></i></button>
							</div>
							
                            <div class="col-sm-4">
                                <select name="customers" multiple id="customer_deselection" class="selection_boxes col-sm-12"></select>
                            </div>
                        </div>
                    </div><!--box-body-->

                    <div class="box-footer">
						<input type="hidden" name="id" value="<?php echo $promocode->id; ?>">
                        <button type="submit" id="submit" class="btn btn-primary pull-right"><i class="fa fa-send"></i><?php echo trans('messages.Send Promocode'); ?></button>
                        <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
                        <button type="button" onclick="window.location.href = '{!! URL::to('admin/promocodes') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}

                </div><!--box-info-->

            </div></div>	  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script>
	$(document).ready(function(){
		//Selecting Customers
		$(document).on("click", "#select", function(){
			if($("#customer_selection").val())
			{
				$clone = [];
				$.each($("#customer_selection").val(), function($i, $val){
					$clone[$i] = $("#customer_selection option[value='" + $val + "']").clone();
					$("#customer_selection option[value='" + $val + "']").remove();
					$("#send_form").append("<input type='hidden' name='customer[]' value='" + $val + "'>");
				})
				$("#customer_deselection").append($clone);
			}
			else
			{
				alert("Please select the customers to proceed the selection");
			}
		})

		//Deselecting customers
		$(document).on("click", "#deselect", function(){
			if($("#customer_deselection").val())
			{
				$clone = [];
				$.each($("#customer_deselection").val(), function($i, $val){
					$clone[$i] = $("#customer_deselection option[value='" + $val + "']").clone();
					$("#customer_deselection option[value='" + $val + "']").remove();
					$("#send_form").find("input[value='" + $val + "']").remove();
				})
				$("#customer_selection").append($clone);
			}
			else
			{
				alert("Please select the customers to proceed the deselection");
			}
		})

		//Submit
		$(document).on("click", '#submit', function(){
			if(!$("#customer_deselection option").length)
			{
				alert("Please select the customers to send promocode");
				return false;
			}
		})
	})
</script>
@endsection
