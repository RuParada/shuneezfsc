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
			
			<h3>Heading</h3>
            
            @if(count($products))
            <ul class="drag_list" id="drag-left">
                @foreach($products->modifiers as $product) 
                    <li>
                        <h4>{!! $product->name->en !!}</h4>
                        <input type="hidden" class="foodics_value" value="{!! $product->hid !!}">
                        <span class="item_tag">{!! $product->hid !!}</span>
						<a class="view"  onclick="viewdetails('{!! $product->hid !!}', 'f');"><i class="fa fa-eye"></i></a>
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
			<h3>Heading</h3>
            @if(count($ingredients))
            <ul class="drag_list no_drag" id="drag-right">
                @foreach($ingredients as $ingredient)
                <li>
                    <h4>{!! $ingredient->ingredient !!}</h4>
                    <input type="hidden" class="item_value" value="{!! $ingredient->id !!}">
                    @if($ingredient->foodics_id != '')
                        <span class="item_tag">{!! $ingredient->foodics_id !!}<a class="remove" onclick="removeFoodics({!! $ingredient->id !!});"><i class="fa fa-times-circle"></i></a></span>
                    @endif
					<a class="view"  onclick="viewdetails('{!! $ingredient->id !!}', 's');"><i class="fa fa-eye"></i></a>
                </li>
                @endforeach
            </ul> <!--drag_list-->
            @endif
        </div> <!--drag_section-->
        
    </div> <!--drag_main-->
</div> <!--full_row-->
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
            var item_id = ($(this).find('.item_value').val());
            updateItem(item_id, foodics_id, draggable)
        }
    });
});

function updateItem(ingredient_id, foodics_id, draggable)
{
    $("#success_msg").hide();
    $.ajax({
    beforeSend : function() {
        $("body").addClass("loading");
    },
    type : "GET",
    dataType : "json",
    url  : "<?php echo URL::to('admin/updatefoodics_ingredient'); ?>",
    data : {'ingredient_id' : ingredient_id, 'foodics_id' : foodics_id},
    async: true,
    success: function(result) {
                $("body").removeClass("loading");
                if(result.success)
                {
                    $("#update_msg").removeClass('error_msg').addClass('success_msg').text(result.msg);
                    draggable.append("<span class='item_tag' onclick='removeFoodics("+ingredient_id+");'>"+foodics_id+"</span>");
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
        window.location.href = url+'/admin/removefoodicsingredient/'+id;
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
      url: "<?php echo URL::to('admin/viewingredient'); ?>",
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

@endsection
