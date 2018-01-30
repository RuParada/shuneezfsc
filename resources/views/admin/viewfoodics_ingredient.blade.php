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
					<ul class="tbl_view">
						<li>
							<span>{!! $ingredient->modifier->name->en !!}</span>
						</li>
					</ul>
					@if(count($ingredient->modifier->options))
						<h4>{!! trans('messages.Options') !!}</h4>
						@foreach($ingredient->modifier->options as $option)
							<ul class="tbl_view">
								<li>
									<span>{!! $option->name->en !!}</span>
									<span>{!! $option->price !!}</span>
								</li>
							</ul>
						@endforeach
					@endif
				</div> <!--modal-body-->
			</div><!--modal-content-->
		</div><!--modal-dialog-->
	</div><!--modal-center-inner-->
</div><!--modal-center-->