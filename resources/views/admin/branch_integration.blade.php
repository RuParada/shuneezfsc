@extends('adminheader')

@section('content')

<div class="content-wrapper">
<section class="content-header">
<p id="update_msg"></p>
@if(Session::has('success'))<p class="success_msg" id="success_msg">{!! Session('success') !!}</p>@endif
<div class="content height">
    <div class="drag_main">
        <div class="drag_section">
            <!--<div class="search_list full_row">
                <input type="text" class="input_control" placeholder="Search...">
                <i class="fa fa-search"></i>
            </div> search_list-->
			<h3>Foodics</h3>
            @if(count($products))
            <ul class="drag_list" id="drag-left">
                @foreach($products->branches as $product) 
                    <li>
                        <h4>{!! $product->name->en !!}</h4>
                        <input type="hidden" class="foodics_value" value="{!! $product->hid !!}">
                        <span class="item_tag">{!! $product->hid !!}</span>
						<!--<a class="view" onclick="viewdetails('{!! $product->hid !!}', 'f');"><i class="fa fa-eye"></i></a>-->
                    </li>
                @endforeach
            </ul> <!--drag_list-->
            @endif
        </div> <!--drag_section-->
        
        <div class="drag_text">Drag to Match</div>
        
        <div class="drag_section">
            <!--<div class="search_list full_row">
                <input type="text" class="input_control" placeholder="Search...">
                <i class="fa fa-search"></i>
            </div> search_list-->
			<h3>Shuneez</h3>
            @if(count($branches))
            <ul class="drag_list no_drag" id="drag-right">
                @foreach($branches as $branches)
                <li>
                    <h4>{!! $branches->branch !!}</h4>
                    <input type="hidden" class="branch_value" value="{!! $branches->id !!}">
                    @if($branches->foodics_id != '')
                        <span class="item_tag">{!! $branches->foodics_id !!}<a class="remove" onclick="removeFoodics({!! $branches->id !!});"><i class="fa fa-times-circle"></i></a></span>
                    @endif
					<!--<a class="view"  onclick="viewdetails({!! $branches->id !!}, 's');"><i class="fa fa-eye"></i></a>-->
                </li>
                @endforeach
            </ul> <!--drag_list-->
            @endif
        </div> <!--drag_section-->
        
    </div> <!--drag_main-->
</div> <!--full_row-->
<div class="modal_load">
</div>
<input type="hidden" id="foodics_id" value="">
<!--JS-->
<script>
$(document).ready(function(){
    $('#drag-left li').draggable({
        connectToSortable: "#sortable",
        appendTo: "body",
        helper: function (event) {
            var src = $(event.currentTarget);
            var foodics_id = $(this).find('.foodics_value').val();
            html = $('<div />').addClass('addon-draggable');
            html.text(foodics_id);
            $("#foodics_id").val(foodics_id);
            return html;
        },

        cursorAt: { left: 20 },     
        delay: 300,
        revert: "invalid"
    });


    $("#drag-right li").droppable({
        greedy: true,
        drop: function( event, ui ) {
            var draggable = $(ui.draggable[0]);         
            console.log(event.currentTarget);
            draggable = $(this);
            var foodics_id = $("#foodics_id").val();
            $(this).find('.item_tag').remove();
            var branch_id = ($(this).find('.branch_value').val());
            updateItem(branch_id, foodics_id, draggable)
        }
    });
});

function updateItem(branch_id, foodics_id, draggable)
{
    $("#success_msg").hide();
    $.ajax({
    beforeSend : function() {
        $("body").addClass("loading");
    },
    type : "GET",
    dataType : "json",
    url  : "<?php echo URL::to('admin/update-foodics-branch'); ?>",
    data : {'branch_id' : branch_id, 'foodics_id' : foodics_id},
    async: true,
    success: function(result) {
                $("body").removeClass("loading");
                if(result.success)
                {
                    $("#update_msg").removeClass('error_msg').addClass('success_msg').text(result.msg);
                    draggable.append("<span class='item_tag'><a class='remove' onclick='removeFoodics("+branch_id+");'><i class='fa fa-times-circle'></i></a>"+foodics_id+"</span>");
                }
                else
                {
                    $("#update_msg").removeClass('success_msg').addClass('error_msg').text(result.msg);
                }
            }
        });
}

function removeFoodics(id)
{
    var url = '{!! URL::to('/') !!}';
    if(confirm('{!! trans('messages.Are you sure you want to remove foodics?') !!}'))
    {
        window.location.href = url+'/admin/remove-foodics-branch/'+id;
    }
}

function viewdetails(id, type)
{
    $.ajax({
      beforeSend: function () {
        $("body").addClass("loading");
      }
      ,
      type: "GET",
      url: "<?php echo URL::to('admin/viewbranch'); ?>",
      data: {'id': id, 'type': type},
      async: true,
      success: function (result) {
        $("body").removeClass("loading");
        $('#modal_view').html(result).modal('show');
      }
    });
}
</script>
    
</section>
</div><!-- /.content-wrapper -->


<!-- modal_view -->
<div id="modal_view" class="modal fade view" role="dialog" data-backdrop="static">
	
</div><!--modal_view-->

<style>
  .modal_load {
    display:    none;
    position:   fixed;
    z-index:    1000;
    top:        0;
    left:       0;
    height:     100%;
    width:      100%;
    background: rgba( 255, 255, 255, .8 ) 
      url(<?php echo URL::to('assets/images/loading.gif');
    ?>) 
    50% 50% 
    no-repeat;
  }
  body.loading {
    overflow: hidden;
  }
  body.loading .modal_load {
    display: block;
  }
</style>
@endsection
