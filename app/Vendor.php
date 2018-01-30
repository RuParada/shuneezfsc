<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Vendor extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;
	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'vendors';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['vendor_key', 'email', 'street', 'country', 'city', 'zipcode', 'latitude', 'longitude', 'sort', 'min_order_value', 'image', 'is_delete', 'status', 'created_by', 'updated_by'];
	
	protected $hidden = ['password', 'remember_token'];

	public function getvendors()
	{
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$vendors = DB::table('vendors')
						->join('vendor_description', 'vendors.id', '=', 'vendor_description.vendor_id')
						->SelectRaw(DB::getTablePrefix().'vendors.*,'.DB::getTablePrefix().'vendor_description.vendor_name as vendor')
						->where('vendor_description.language', $language)
						->where('vendors.is_delete', 0)
						->paginate(10);
		//echo '<pre>'; print_r($vendors); exit;				 
		return $vendors;
	}
}
