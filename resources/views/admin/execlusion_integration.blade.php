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
                @foreach($products->inventory_items as $product) 
                    <li>
                        <h4>{!! $product->name->en !!}</h4>
                        <input type="hidden" class="foodics_value" value="{!! $product->hid !!}">
                        <span class="item_tag">{!! $product->hid !!}</span>
						<a class="view" data-toggle="modal" data-target="#modal_view"><i class="fa fa-eye"></i></a>
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
            @if(count($execlusions))
            <ul class="drag_list no_drag" id="drag-right">
                @foreach($execlusions as $execlusion)
                <li>
                    <h4>{!! $execlusion->execlusion !!}</h4>
                    <input type="hidden" class="item_value" value="{!! $execlusion->id !!}">
                    @if($execlusion->foodics_id != '')
                        <span class="item_tag">{!! $execlusion->foodics_id !!}<a class="remove" onclick="removeFoodics({!! $execlusion->id !!});"><i class="fa fa-times-circle"></i></a></span>
                    @endif
					<!--<a class="view" data-toggle="modal" data-target="#modal_view"><i class="fa fa-eye"></i></a>-->
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
            var execlusion_id = ($(this).find('.item_value').val());
            updateItem(execlusion_id, foodics_id, draggable)
        }
    });
});

function updateItem(execlusion_id, foodics_id, draggable)
{
    $("#success_msg").hide();
    $.ajax({
    beforeSend : function() {
        $("body").addClass("loading");
    },
    type : "GET",
    dataType : "json",
    url  : "<?php echo URL::to('admin/updatefoodics_execlusion'); ?>",
    data : {'execlusion_id' : execlusion_id, 'foodics_id' : foodics_id},
    async: true,
    success: function(result) {
                $("body").removeClass("loading");
                if(result.success)
                {
                    $("#update_msg").removeClass('error_msg').addClass('success_msg').text(result.msg);
                    draggable.append("<span class='item_tag'><a class='remove' onclick='removeFoodics("+execlusion_id+");'><i class='fa fa-times-circle'></i></a>"+foodics_id+"</span>");
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
        window.location.href = url+'/admin/removefoodicsexeclusion/'+id;
    }
}
</script>
    
</section>
</div><!-- /.content-wrapper -->

@endsection
