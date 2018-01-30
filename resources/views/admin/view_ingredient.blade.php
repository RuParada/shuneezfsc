<div class="modal-center">
	<div class="modal-center-inner">
		<div class="modal-dialog modal-sm">		
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></button>
					<h2 class="modal-title">Info</h2>
				</div><!--modal-header-->
				
				<div class="modal-body">
					<h5>{!! $ingredient->ingredient_name !!}</h5>
					@if(count($ingredient->ingredientlists))
						@foreach($ingredient->ingredientlists as $ingredientlist)
							<ul class="tbl_view">
								<li>
									<span>{!! $ingredientlist->ingredientlist_name !!}</span>
									<span>{!! $ingredientlist->price !!}</span>
								</li>
							</ul>
						@endforeach
					@endif
				</div> <!--modal-body-->
			</div><!--modal-content-->
		</div><!--modal-dialog-->
	</div><!--modal-center-inner-->
</div><!--modal-center-->