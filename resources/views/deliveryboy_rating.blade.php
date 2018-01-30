<div class="modal-dialog">

<!-- Modal content-->
<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">{!! trans('frontend.Rating Driver') !!}</h4>
  </div>
  <div class="modal-body">
    {!! Form::open(['id' => 'review_form']) !!}
    <div class="col-sm-12 pad0">
    <span id="rating_msg" style="color: green"></span>
      <h4 class="bold text-center">{!! trans('frontend.Ratings') !!}</h4>
        <div class="col-sm-12 plr0 ptb10 text-center">  
          <input type="hidden" value="0" name="rating" id="rating_value">
          <div class="starbox ghosting autoupdate" data-button-count="10" data-start-value="0"></div>
          <span id="rating_error" style="color: red"></span>
        </div>
        <br>
      <h4 class="bold text-center">{!! trans('frontend.Comments') !!}</h4>
      <div class="form_group">
        <textarea class="form_control col-sm-12" rows="5"  placeholder="Enter your Review" name="review" id="review"></textarea>
        <span id="review_error" style="color: red"></span>
      </div>
      <input type="hidden" name="order_id" value="{!! $order_id !!}">
      <button type="button" class="view_ord1" onclick="addRating();">Submit</button>
    </div>
    {!! Form::close() !!}
  </div>
</div>

</div>

<script type="text/javascript">
  /*Star Rating*/
jQuery(function() {
      jQuery('.starbox').each(function() {
        var starbox = jQuery(this);
        starbox.starbox({
          average: starbox.attr('data-start-value'),
          changeable: starbox.hasClass('unchangeable') ? false : starbox.hasClass('clickonce') ? 'once' : true,
          ghosting: starbox.hasClass('ghosting'),
          autoUpdateAverage: starbox.hasClass('autoupdate'),
          buttons: starbox.hasClass('smooth') ? false : starbox.attr('data-button-count') || 5,
          stars: starbox.attr('data-star-count') || 5
        }).bind('starbox-value-changed', function(event, value) {
          if(starbox.hasClass('random')) {
            var val = Math.random();
            starbox.next().text('Random: '+val);
            return val;
          } else {
            $('input[name="rating"]').val(value);
          }
        }).bind('starbox-value-moved', function(event, value) {
          $('input[name="rating"]').val(value);
        });
      });
    });

function addRating()
{
    var rating = $("#rating_value").val();
    var review = $("#review").val();
    
    if( rating == '') {
      $("#rating_error").text("{!! trans('frontend.Please enter rating') !!}");
      return false;
    }
    if( review == '') {
      $("#review_error").text("{!! trans('frontend.Please enter review') !!}");
      return false;
    }
    if(rating != 0)
    {
      $("#rating_error").text('');
      $("#review_error").text('');
      $.ajax({
      beforeSend : function() {
      $("body").addClass("loading");
      },
      type : "POST",
      dataType : "json",
      url  : "<?php echo URL::to('addrating'); ?>",
      data : $('#review_form').serialize(),
      async: true,
      success: function(result) {
        $("body").removeClass("loading");
        $("#rating_msg").text("{!! trans('frontend.Thanks for your rating') !!}");
        setInterval(function()
        {
          window.location.reload();
        }, 1500);
      }
    });
    }
    else
    {
      $("#rating_error").text("{!! trans('frontend.Please enter rating') !!}");
    }
}

</script>