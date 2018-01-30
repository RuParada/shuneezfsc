<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Order extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'orders';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['order_key', 'invoice_number', 'currency_code', 'customer_key', 'customer_first_name', 'customer_last_name', 'customer_email', 'country_code', 'customer_mobile_number', 'vendor_key', 'shop_key' ,'order_total', 'sub_total', 'service_tax_percentage', 'service_tax'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	public function getorders()
	{
		$orders = DB::table('orders')->orderby('id', 'desc')->paginate(10);	
		
		return $orders;
	} 
	
	public function getbranch_orders($branch_id)
	{
		$orders = DB::table('orders')->where('branch_id', $branch_id)->orderby('id', 'desc')->paginate(10);	
		
		return $orders;
	} 

	public function getorder_details($cid)
	{
		$orders = DB::table('orders')->where('customer_id', $cid)->orderby('id', 'desc')->paginate(10);		
		return $orders;
	} 
}
