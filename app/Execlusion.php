<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Execlusion extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;
	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'execlusions';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['status'];
	
	

	public function getexeclusions()
	{

		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$execlusions = DB::table('execlusions')
						->join('execlusion_description', 'execlusions.id', '=', 'execlusion_description.execlusion_id')
						->SelectRaw(DB::getTablePrefix().'execlusions.*,'.DB::getTablePrefix().'execlusion_description.execlusion_name as execlusion')
						->where('execlusion_description.language', $language)
						->orderby('execlusions.id', 'desc')
						->paginate(10);
						 
		return $execlusions;
	}

	public function getAllExeclusions()
	{

		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$execlusions = DB::table('execlusions')
						->join('execlusion_description', 'execlusions.id', '=', 'execlusion_description.execlusion_id')
						->SelectRaw(DB::getTablePrefix().'execlusions.*,'.DB::getTablePrefix().'execlusion_description.execlusion_name as execlusion')
						->where('execlusion_description.language', $language)
						->orderby('execlusions.id', 'desc')
						->get();
						 
		return $execlusions;
	}

	public function getActiveExeclusions()
	{

		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$execlusions = DB::table('execlusions')
						->join('execlusion_description', 'execlusions.id', '=', 'execlusion_description.execlusion_id')
						->SelectRaw(DB::getTablePrefix().'execlusions.*,'.DB::getTablePrefix().'execlusion_description.execlusion_name as execlusion')
						->where('execlusion_description.language', $language)
						->where('status', 1)
						->orderby('execlusions.id', 'desc')
						->get();
						 
		return $execlusions;
	}

	public function getVendorExeclusions($item_id)
	{

		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$execlusions = DB::table('vendor_item_execlusions')
						->join('execlusions', 'vendor_item_execlusions.execlusion_id', '=', 'execlusions.id')
						->join('execlusion_description', 'vendor_item_execlusions.execlusion_id', '=', 'execlusion_description.execlusion_id')
						->SelectRaw(DB::getTablePrefix().'execlusions.*,'.DB::getTablePrefix().'execlusion_description.execlusion_name as execlusion')
						->where('execlusion_description.language', $language)
						->where('item_id', $item_id)
						->where('status', 1)
						->orderby('execlusions.id', 'desc')
						->get();
						 
		return $execlusions;
	}

	public function getAllExeclusionList()
	{

		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$execlusionlist = DB::table('execlusionlist')
						->join('execlusionlist_description', 'execlusionlist.id', '=', 'execlusionlist_description.execlusionlist_id')
						->SelectRaw(DB::getTablePrefix().'execlusionlist.*,'.DB::getTablePrefix().'execlusionlist_description.execlusionlist_name as execlusionlist')
						->where('execlusionlist_description.language', $language)
						->orderby('execlusionlist.id', 'desc')
						->get();
						 
		return $execlusionlist;
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
