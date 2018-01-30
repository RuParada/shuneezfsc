<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h4>Invoice Number : <?php echo $order['details']->invoice_number; ?></h4>
<!--            
<div class="col-md-3">
	<div class="img2">
		<img src="img/product-2.png" style="width:100px;height:100px;">
	</div>
</div>

<div class="clr"></div>
<hr>-->
<?php foreach($order['items'] as $key => $item) { ?>
<p><?php echo $item->item_name; ?> X <?php echo $item->quantity; ?><span style="float:right;"><?php echo getdefault_currency().' '.($item->quantity * $item->price); ?></span></p>

<?php
if($item->is_ingredients)
{
?>
<p style="font-weight:bold;font-size:16px;width:100%;">Choices</p>
<?php
foreach($item->ingredients as $ingredient) { 
?>
<p><?php echo $ingredient->ingredient_name; ?>X <?php echo $item->quantity; ?> <span class="view_od_price"><?php echo getdefault_currency().' '.($item->quantity * $ingredient->ingredient_price); ?></span></p>
<hr>
<?php } } } ?>
<p>Subtotal<span class="view_od_price"><?php echo getdefault_currency().' '.$order['details']->sub_total; ?></span></p>
<p style="color:#662d91;font-size:16px;">Delivery Fee<span class="view_od_price1"><?php echo getdefault_currency().' '.$order['details']->delivery_fee; ?></span></p>
<p>Vat(%) <span class="view_od_price"><?php echo getdefault_currency().' '.$order['details']->vat; ?></span></p>
<hr>
<p style="color:#662d91;font-size:16px;">Total<span class="view_od_price1"><?php echo getdefault_currency().' '.$order['details']->order_total; ?></span></p>

<style>

div#vieworder-modal {
width: 50%;
background: white;
max-height: 500px;
margin: 10% auto;
padding: 10px;
}
</style>
