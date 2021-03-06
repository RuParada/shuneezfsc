@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3);?>
<div class="content-wrapper">

	<section class="content-header">
		<h1>Manage Cities</h1>
		@if(Session::has('success'))<p class="success_msg"><?php echo Session::get('success'); ?></p> @endif
		@if(Session::has('error'))<p class="error_msg"><?php echo Session::get('error')->first('city'); ?></p>@endif 
	</section>

	<!-- Main content -->
	<section class="content">
          
	<div class="box">                
        <div class="box-body">
			<p class="top_add_btn"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCity"><i class="fa fa-plus"></i>Add City</button></p>
			<div class="row">
			{!! Form::open(array('url' => 'admin/filtercity', 'method' => 'get')) !!}
			<div class="col-md-3 form-group">
          <label>City</label>
          <div class="input-group">
            <div class="input-group-addon"> <i class="fa fa-shopping-cart"></i> </div>
            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                    </div> <!-- input-group -->
        </div> <!-- form-group -->
        <div class="col-md-3 form-group full_selectLists">
          <label>Select Status</label>
          <div class="input-group">
            <div class="input-group-addon"> <i class="fa fa-building"></i> </div>
            <select class="selectLists" name="status">
              <option value="">Select Status</option>
              <option value="1" <?php echo (Input::get('status') == '1') ? 'selected' : ''; ?>>Active </option>
              <option value="0" <?php echo (Input::get('status') == '0') ? 'selected' : ''; ?>>Inactive</option>
            </select>
          </div>
        </div> <!-- form-group -->
        
        
        <div class="col-md-3 form-group">
          <label>Filter</label>
          <div class="input-group">
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>Search City</button>
                    </div> <!-- input-group -->
        </div> <!-- form-group -->
        {!! Form::close() !!}
			</div> <!-- row -->
            <table id="dataTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
					  <th>S.No</th>
                      <th>City</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                  <?php if(count($cities) > 0) { 
                      $i = ($cities->currentPage() == 1) ? 1 : (($cities->currentPage() - 1) * $cities->perPage()) + 1;
                      foreach ($cities as $city) {
                  ?>
                    <tr>
					  <td><?php echo $i; ?></td>
                      <td><?php echo $city->city; ?></td>
                      <td class="onoff" width="50">
                        <input type="checkbox" name="active_inactive" <?php echo ($city->status == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $city->id; ?>" onclick="status_change(<?php echo $city->id; ?>, <?php echo $city->status; ?>);">
                        <label class="onoff_lbl" for="<?php echo $city->id; ?>"></label>
                        </td>
                        <td class="action_btns" width="50">
                        <a href="{!! URL::to('admin/editcity/'.$city->id) !!}" data-toggle="modal" data-target="#editCity" class="edit_btn"><i class="fa fa-pencil"></i></a>
                        <a href="#" class="delete_btn" onclick="delete_city(<?php echo $city->id; ?>);"><i class="fa fa-trash"></i></a>
                   </td>
                    </tr>
                  <?php $i++; } } else { ?>
						<tr><td colspan="5" style="text-align:center">No City Found</td></tr>
				  <?php } ?>
                </tbody>
            </table>
            <?php echo "<div class='row-page entry'>";
              if($cities->currentPage()==1)
              {
                $count=$cities->count();
              }
              else if($cities->perPage()>$cities->count())
              {
                $count= ($cities->currentPage()-1)*$cities->perPage()+$cities->count(); 
              }
              else
              {
                $count= $cities->perPage()+$cities->count(); 
              }
            // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
             if($count > 0)
              {
                $start=($cities->currentPage()==1)?1:$count-$cities->count()+1;
              }
              else
              {
                $start = 0;
              }

              echo "<span style='float:right'>Showing ".$start." to ".$count." of ".$cities->total(). " entries</span>"; echo $cities->appends(['state_id' => Input::get('state_id'), 'name' => Input::get('name'), 'status' => Input::get('status')])->render();
              echo '</div>';
            ?>
        </div><!-- /.box-body -->
    </div><!-- /.box -->		  
          
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Add City -->
<div class="modal fade bs-example-modal-sm" id="addCity" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Add City</h4>
      </div>	  
	    {!! Form::open(array('url' => 'admin/addcity')) !!}		
		<div class="modal-body">         
        <div class="form-group">
			<label>City</label>
			<input type="text" name="city" required class="form-control" placeholder="City">
		  </div>       
        <div class="form-group radio_btns">
          <label> <input type="radio" name="status" checked value="1"> Active</label>
          <label> <input type="radio" name="status" value="0"> Inactive</label>
        </div>     
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i>Save City</button>
		  </div>
		  </div>
	  {!! Form::close() !!}
    </div>
  </div>
</div>

<!-- Edit City -->
<div class="modal fade bs-example-modal-sm" id="editCity" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
          
      <div class="editType">        
              
      </div>
    </div>
  </div>
</div>

<script>
    function status_change(id, status)
    {
        var url = "<?php echo URL::to(''); ?>";
    	$.ajax({
         type: "GET",
         url: url+"/admin/change_citystatus",
         dataType:"json",
         data: {'id' : id, 'status' : status},
         async: true,
         success:  function(result){
            var newstatus = (status == 1) ? 0 : 1;
            $("#"+id).attr("onclick", "status_change("+id+","+newstatus+");");
            $('.success_msg').html(result.msg)
          }
        });
    }

    function delete_city(id)
    {
    	if(confirm('Are you sure you want delete this city ?'))
    	{
    		window.location = 'deletecity/'+id;
    	}
    }

</script>
@endsection
