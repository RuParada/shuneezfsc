<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Deliveryboy extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'deliveryboys';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['deliveryboy_key', 'email', 'mobile', 'status', 'is_delete', 'availability', 'created_by', 'updated_by', 'device_type', 'device_id', 'gcm_id'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password'];
	public function getdelivery_boys() {
		
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
        $delivery_boys = DB::table('deliveryboys')
						 ->join('deliveryboy_description', 'deliveryboys.id', '=', 'deliveryboy_description.deliveryboy_id')
						 ->join('branch_description', 'deliveryboys.branch_id', '=', 'branch_description.branch_id')
						 ->SelectRaw(DB::getTablePrefix().'deliveryboys.*,'.DB::getTablePrefix().'deliveryboy_description.deliveryboy_name as name,'.DB::getTablePrefix().'branch_description.branch_name as branch')
						 ->where('deliveryboys.is_delete', 0)
						 ->where('deliveryboy_description.language', $language)
						 ->where('branch_description.language', $language)
						 ->orderby('deliveryboys.id', 'desc')
						 ->paginate(10);

        return $delivery_boys;
    }
    
    public function getdeliveryboy($id) {
		
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
        $delivery_boy = DB::table('deliveryboys')
						 ->join('deliveryboy_description', 'deliveryboys.id', '=', 'deliveryboy_description.deliveryboy_id')
						 ->join('branch_description', 'deliveryboys.branch_id', '=', 'branch_description.branch_id')
						 ->SelectRaw(DB::getTablePrefix().'deliveryboys.*,'.DB::getTablePrefix().'deliveryboy_description.deliveryboy_name,'.DB::getTablePrefix().'branch_description.branch_name as branch')
						 ->where('deliveryboys.id', $id)
						 ->where('deliveryboy_description.language', $language)
						 ->where('branch_description.language', $language)
						 ->first();

        return $delivery_boy;
    }

    public function getbranch_deliveryboys($branch_id)
    {
    	$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
    	$delivery_boys = DB::table('deliveryboys')
						->join('deliveryboy_description', 'deliveryboys.id', '=', 'deliveryboy_description.deliveryboy_id')
						->SelectRaw(DB::getTablePrefix().'deliveryboys.*,'.DB::getTablePrefix().'deliveryboy_description.deliveryboy_name as deliveryboy')
						->where('deliveryboys.branch_id', $branch_id)
						->where('deliveryboys.is_delete', 0)
						->where('deliveryboys.status', 1)
						->where('deliveryboys.availability', 1)
						->where('deliveryboys.is_logout', 0)
						->where('deliveryboy_description.language', $language)
						->get();
		return $delivery_boys;
    }
    
    public function getbranch_alldeliveryboys($branch_id)
    {
    	$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
    	$delivery_boys = DB::table('deliveryboys')
						->join('deliveryboy_description', 'deliveryboys.id', '=', 'deliveryboy_description.deliveryboy_id')
						->SelectRaw(DB::getTablePrefix().'deliveryboys.*,'.DB::getTablePrefix().'deliveryboy_description.deliveryboy_name as name')
						->where('deliveryboys.branch_id', $branch_id)
						->where('deliveryboys.is_delete', 0)
						->where('deliveryboy_description.language', $language)
						->paginate(10);
		return $delivery_boys;
    }

    public function getDookDeliveryBoys() {
		
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
        $delivery_boys = DB::table('deliveryboys')
						 ->join('deliveryboy_description', 'deliveryboys.id', '=', 'deliveryboy_description.deliveryboy_id')
						 ->SelectRaw(DB::getTablePrefix().'deliveryboys.*,'.DB::getTablePrefix().'deliveryboy_description.deliveryboy_name as deliveryboy')
						 ->where('is_delete', 0)
						 ->where('type', 1)
						 ->where('deliveryboy_description.language', $language)
						 ->get();

        return $delivery_boys;
    }

    public function getAllDeliveryBoys() {
		
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
        $delivery_boys = DB::table('deliveryboys')
						 ->join('deliveryboy_description', 'deliveryboys.id', '=', 'deliveryboy_description.deliveryboy_id')
						 ->SelectRaw(DB::getTablePrefix().'deliveryboys.*,'.DB::getTablePrefix().'deliveryboy_description.deliveryboy_name as deliveryboy')
						 ->where('is_delete', 0)
						 ->where('deliveryboy_description.language', $language)
						 ->get();

        return $delivery_boys;
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
