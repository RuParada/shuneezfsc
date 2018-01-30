@extends('adminheader')

@section('content')

<div class="content-wrapper">
<section class="content-header">
<p id="update_msg"></p>
@if(Session::has('success'))<p class="success_msg" id="success_msg">{!! Session('success') !!}</p>@endif
<div class="content height">
    <div class="drag_main">
        <div class="drag_section">
            <div class="search_list full_row">
                <input type="text" class="input_control" placeholder="Search...">
                <i class="fa fa-search"></i>
            </div> <!--search_list-->
            
            @if(count($delivery_zones))
            <ul class="drag_list" id="drag-left">
                @foreach($delivery_zones->delivery_zones as $delivery_zone) 
                    <li>
                        <h4>{!! $delivery_zone->name->en !!}</h4>
                        <input type="hidden" class="foodics_value" value="{!! $delivery_zone->hid !!}">
                        <span class="item_tag">{!! $delivery_zone->hid !!}</span>
                    </li>
                @endforeach
            </ul> <!--drag_list-->
            @endif
        </div> <!--drag_section-->
        
        <div class="drag_text">Drag to Match</div>
        
        <div class="drag_section">
            <div class="search_list full_row">
                <input type="text" class="input_control" placeholder="Search...">
                <i class="fa fa-search"></i>
            </div> <!--search_list-->
            @if(count($addresstype))
            <ul class="drag_list no_drag" id="drag-right">
                @foreach($addresstype as $address)
                <li>
                    <h4>{!! $address->addresstype !!}</h4>
                    <input type="hidden" class="item_value" value="{!! $address->id !!}">
                    @if($address->foodics_id != '')
                        <span class="item_tag" onclick="removeFoodics({!! $address->id !!});">{!! $address->foodics_id !!}</span>
                    @endif
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
            draggable.append("<span class='item_tag'>"+foodics_id+"</span>");
            updateItem(item_id, foodics_id)
        }
    });
});

function updateItem(addresstype_id, foodics_id, draggable)
{
    $("#success_msg").hide();
    $.ajax({
    beforeSend : function() {
        $("body").addClass("loading");
    },
    type : "GET",
    dataType : "json",
    url  : "<?php echo URL::to('admin/updatefoodics_addresstype'); ?>",
    data : {'addresstype_id' : addresstype_id, 'foodics_id' : foodics_id},
    async: true,
    success: function(result) {
                $("body").removeClass("loading");
                if(result.success)
                {
                    $("#update_msg").removeClass('error_msg').addClass('success_msg').text(result.msg);
                    draggable.append("<span class='item_tag' onclick='removeFoodics("+addresstype_id+");'>"+foodics_id+"</span>");
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
        window.location.href = url+'/admin/removefoodicsaddresstype/'+id;
    }
}
</script>
    
</section>
</div><!-- /.content-wrapper -->

@endsection
