@extends('header')

@section('content')
      <?php
      // echo "<pre>";
      // print_r($items);
      // exit;
      ?>

    <div class="container">
       <div class="verfiy-page">
              <div class="bred1">
                <a href="{!! URL::to('/') !!}" class="bred2"><?php echo trans('frontend.Home'); ?></a><img src="assets/img/breadcrumb-arrow.png" class="bred"><a href="#"><?php echo trans('frontend.My Order'); ?></a>
              </div>
        </div>
        <div class="col-md-3 pad0">
            <div class="inn_mnu">
              <ul class="in_mnu">     
                <li class="active"><a href="{!! URL::to('/myorder') !!}"><?php echo trans('frontend.My Order'); ?></a></li>           
                <li><a href="{!! URL::to('/edit_profile') !!}"><?php echo trans('frontend.Edit Profile'); ?></a></li>
                <li><a href="{!! URL::to('/address_book') !!}"><?php echo trans('frontend.Address Book'); ?></a></li>
                
              </ul>
            </div>
          </div>
        <div class="col-md-9 ">
            <div class="col-md-12">
                <p style="font-size:24px;color:#3e3e3e;"><?php echo trans('frontend.My Order'); ?></p>
                <hr>
            </div>
            <div class="lis_sect col-md-12">

            <?php
                if(count($orders))
                {
                foreach($orders as $order)
                {
                  if($order['order']->order_status == 'p')
                  {
                    $order_status = trans('frontend.Pending');
                  }
                  elseif($order['order']->order_status == 'as')
                  {
                    $order_status = trans('frontend.Assigned');
                  }
                  elseif($order['order']->order_status == 'd')
                  {
                    $order_status = trans('frontend.Delivered');
                  }
                  elseif($order['order']->order_status == 'ca')
                  {
                    $order_status = trans('frontend.Cancelled');
                  }
                  elseif($order['order']->order_status == 'c')
                  {
                    $order_status = trans('frontend.Confirmed');
                  }
                  elseif($order['order']->order_status == 'o')
                  {
                    $order_status = trans('frontend.Out for delivery');
                  }
                  elseif($order['order']->order_status == 'a')
                  {
                    $order_status = trans('frontend.Accepted');
                  }
            ?>
                <div class="col-md-6">
                    <p style="font-size:18px;color:#3e3e3e;"><?php echo $order['order']->branch_name; ?></p>
                    <p style="font-size:15px;color:#3e3e3e;font-style: italic;margin-top:-10px;"><?php echo date('d-M-Y g:i A', strtotime($order['order']->order_date)); ?></p>
                    <p style="font-size:15px;color:#3e3e3e;"><?php echo $order['items']; ?></p>
                    <p style="font-size:15px;color:#3e3e3e;font-weight: bold;"><?php echo getdefault_currency().' '.$order['order']->order_total; ?></p>
                    <button type="button" class="view_ord" data-toggle="modal" onclick="getorder(<?php echo $order['order']->id; ?>);" data-target="#vieworder-modal">View order</button>
                    @if ( $order['order']->order_status == 'd' && $order['order']->dook_rating_id == '' )
                      <button type="button" class="view_ord" onclick="rateDriver({!! $order['order']->id !!});">Rating Driver</button>
                    @endif
                    <!--<button type="button" class="view_ord">Reorder</button>-->
                </div>
            
                <div class="col-md-3">
                    <p style="color:#3e3e3e;font-size:15px;"><?php echo trans('frontend.Deliver Status'); ?>: <span style="color:#662d91;font-size:16px;"><?php echo $order_status; ?></span></p>
                </div>
                <div class="clr"></div>
                <hr>
            <?php
                } }
            ?>
            </div>
        </div>
        <div class="clr"></div>
    </div>
    
      <!-- Button trigger modal -->

      <!--View Order Modal Dialog-->
<div class="modal fade" id="vieworder-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="viewmodal-container">
        </div>
    </div>
</div>
<!--End of View Order Modal Dialog-->
	<script>
      $('#myTab a').click(function (e) {
          e.preventDefault()
          $(this).tab('show')
      });
    </script>
    <script type="text/javascript">   
      $('.slidingDiv').hide();
      $('.show_hide').click(function(e){ // <----you missed the '.' here in your selector.
          e.stopPropagation();
          $('.slidingDiv').slideToggle();
      });
      $('.slidingDiv').click(function(e){
          e.stopPropagation();
      });

      $(document).click(function(){
           $('.slidingDiv').slideUp();
      });
      function getorder(id)
      {
        $.ajax({
                beforeSend: function () {
                    $("body").addClass("loading");
                },
                type: "GET",
                url: "<?php echo URL::to('/getorder'); ?>",
                data: {'id': id},
                async: true,
                success: function (result) {
						$("body").removeClass("loading");
						$("#vieworder-modal").html(result).modal('show');
                }
            }); 
      }

      function rateDriver(id)
      {
        $.ajax({
                beforeSend: function () {
                    $("body").addClass("loading");
                },
                type: "GET",
                url: "<?php echo URL::to('/rate-driver'); ?>",
                data: {'id': id},
                async: true,
                success: function (result) {
                $("body").removeClass("loading");
                $("#rating-modal").html(result).modal('show');
                }
            }); 
      }
    </script>
    
@endsection

<!-- Modal -->
<div id="rating-modal" class="modal fade" role="dialog">

</div>