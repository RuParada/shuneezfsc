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
					<h3 class="title">{!! $item->product->name->en !!}</h3>
					<ul class="tbl_view">
						<li>
							<span>{!! trans('messages.Category') !!}</span>
							<span>{!! $item->product->category_name !!}</span>
						</li>
					</ul>

					@if(count($item->product->sizes))
						<h4>{!! trans('messages.Sizes') !!}</h4>
						@foreach($item->product->sizes as $size)
							<ul class="tbl_view">
								<li>
									<span>{!! $size->name->en !!}</span>
									<span>{!! $size->price !!}</span>
								</li>
							</ul>
						@endforeach
					@endif
					
					@if(count($item->product->modifiers))
						<h4>{!! trans('messages.Modifiers') !!}</h4>
						@foreach($item->product->modifiers as $modifier)
							<h5>{!! $modifier->name->en !!}</h5>
							@if(count($modifier->options))
								@foreach($modifier->options as $option)
									<ul class="tbl_view">
										<li>
											<span>{!! $option->name !!}</span>
											<span>{!! $option->price !!}</span>
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