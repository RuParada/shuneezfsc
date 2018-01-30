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
					<h3 class="title">{!! $item->item_name !!}</h3>
					
					<ul class="tbl_view">
						<li>
							<span>{!! trans('messages.Category') !!}</span>
							<span>{!! $item->category_name !!}</span>
						</li>
						<li>
							<span>{!! trans('messages.Price') !!}</span>
							<span>{!! $item->price !!}</span>
						</li>
					</ul>
					
					@if($item->is_size)
						<h4>{!! trans('messages.Sizes') !!}</h4>
						@foreach($item->sizelist as $size)
							<ul class="tbl_view">
								<li>
									<span>{!! $size->size_name !!}</span>
									<span>{!! $size->price !!}</span>
								</li>
							</ul>
						@endforeach
					@endif
					
					@if($item->is_ingredients)
						<h4>{!! trans('messages.Ingredients') !!}</h4>
						@foreach($item->ingredients as $ingredient)
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
						@endforeach
					@endif
				</div> <!--modal-body-->
			</div><!--modal-content-->
		</div><!--modal-dialog-->
	</div><!--modal-center-inner-->
</div><!--modal-center-->