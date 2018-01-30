<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Vendoritem extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'vendor_items';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['vendor_id', 'category_id', 'item_key', 'cuisine_id', 'subcategory_id', 'price', 'approved', 'weight', 'units', 'serve_for', 'preparation_time', 'availability', 'featured', 'image', 'is_delete', 'status'];

	public function getvendoritems()
	{
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$items = DB::table('vendor_items')
						->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
						->join('category_description', 'vendor_items.category_id', '=', 'category_description.category_id')
						->SelectRaw(DB::getTablePrefix().'vendor_items.*,'.DB::getTablePrefix().'vendor_item_description.item_name as item,'.DB::getTablePrefix().'category_description.category_name as category')
						->where('vendor_item_description.language', $language)
						->where('category_description.language', $language)
						->where('vendor_items.is_delete', 0)
						->orderby('vendor_items.sort_number', 'asc')
						->paginate(10);
						 
		return $items;
	}
	
	public function getvendoritem($id)
	{
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$item = DB::table('vendor_items')
						->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
						->join('category_description', 'vendor_items.category_id', '=', 'category_description.category_id')
						->join('vendor_description', 'vendor_items.vendor_id', '=', 'vendor_description.vendor_id')
						->join('cuisine_description', 'vendor_items.cuisine_id', '=', 'cuisine_description.cuisine_id')
						->SelectRaw(DB::getTablePrefix().'vendor_items.*,'.DB::getTablePrefix().'vendor_item_description.item_name as item,'.DB::getTablePrefix().'vendor_item_description.item_description as description')
						->where('vendor_item_description.language', $language)
						->where('category_description.language', $language)
						->where('vendor_items.id', $id)
						->first();
						 
		return $item;
	}

	public function getAllItems()
	{
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$items = DB::table('vendor_items')
						->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
						->join('category_description', 'vendor_items.category_id', '=', 'category_description.category_id')
						->SelectRaw(DB::getTablePrefix().'vendor_items.*,'.DB::getTablePrefix().'vendor_item_description.item_name as item,'.DB::getTablePrefix().'category_description.category_name as category')
						->where('vendor_item_description.language', $language)
						->where('category_description.language', $language)
						->where('vendor_items.is_delete', 0)
						->orderby('vendor_items.sort_number', 'asc')
						->get();
						 
		return $items;
	}
	
	public function rules($inputs)
	{
	  $error_count = 0;
	  foreach($inputs as $key => $val)
	  {
		$array_error[$key] = ($val == '') ? 'required': '';
		if($val == '')
		{
			$error_count = 1;
		}
	  }

	  return array('array_error' => $array_error, 'error_count' => $error_count);
	}
}
