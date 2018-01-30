@extends('adminheader')
@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">
   <section class="content-header">
      <h1><?php echo trans('messages.Manage Ingredient Type'); ?> </h1>
      @if(Session::has('success'))
      <p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p>
      @endif
      <p id="success_msg"></p>
   </section>
   <!-- Main content -->
   <section class="content">
      <div class="box">
         <div class="box-body">
           <table id="dataTable" class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th><?php echo trans('messages.S.No'); ?></th>
                     <th><?php echo trans('messages.Ingredient Type'); ?></th>
                     <th><?php echo trans('messages.Ingredient'); ?></th>
                     <th><?php echo trans('messages.Action'); ?></th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     if (count($ingredients) > 0) {
                         $i = ($ingredients->currentPage() == 1) ? 1 : (($ingredients->currentPage() - 1) * $ingredients->perPage()) + 1;
                         foreach ($ingredients as $ingredient) {
                             ?>
                  <tr>
                     <td><?php echo $i; ?></td>
                     <td><?php echo $ingredient->ingredient_type; ?></td>
                     <td><?php echo $ingredient->ingredient; ?></td>
                      <td class="action_btns" width="80">
                        <a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_ingredientlist('.$ingredient->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
                     </td>
                  </tr>
                  <?php
                     $i++;
                     }
                     } else {
                     ?>
                  <tr>
                     <td colspan="8" style="text-align:center"><?php echo trans('messages.No Ingredients Found'); ?></td>
                  </tr>
                  <?php } ?>
               </tbody>
            </table>
            <?php
               echo "<div class='row-page entry'>";
               if ($ingredients->currentPage() == 1) {
                    $count = $ingredients->count();
                } else if ($ingredients->perPage() > $ingredients->count()) {
                    $count = ($ingredients->currentPage() - 1) * $ingredients->perPage() + $ingredients->count();
                } else {
                    $count = $ingredients->currentPage() * $ingredients->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($ingredients->currentPage() == 1) ? 1 : ($ingredients->perPage() * ($ingredients->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }
               
               echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $ingredients->total() . " ".trans('messages.entries')."</span>";
               echo $ingredients->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
               echo '</div>';
               ?>
         </div>
         <!-- /.box-body -->
      </div>
      <!-- /.box -->		  
   </section>
   <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
   function delete_ingredientlist(id)
   {
       if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
       {
           window.location = 'deleteingredientlist/' + id;
       }
   }   
</script>
@endsection
