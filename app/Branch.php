<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Branch extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;
	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'branches';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['vendor_id', 'branch_key', 'email', 'mobile', 'password', 'street', 'country', 'city', 'zipcode', 'latitude', 'longitude', 'image', 'is_delete', 'status', 'created_by', 'updated_by'];
	
	protected $hidden = ['password', 'remember_token'];

	public function getbranches()
	{
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$branches = DB::table('branches')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->SelectRaw(DB::getTablePrefix().'branches.*,'.DB::getTablePrefix().'branch_description.branch_name as branch')
						->where('branch_description.language', $language)
						->where('branches.is_delete', 0)
						->orderby('branches.id', 'desc')
						->paginate(10);
		return $branches;
	}
	
	public function getbranch($id)
	{
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$branch = DB::table('branches')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->SelectRaw(DB::getTablePrefix().'branches.*,'.DB::getTablePrefix().'branch_description.branch_name as branch')
						->where('branch_description.language', $language)
						->where('branches.id', $id)
						->first();
		return $branch;
	}
	
	public function getsearchbranch($id, $order_type)
	{
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$branch = DB::table('branches')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->SelectRaw(DB::getTablePrefix().'branches.*,'.DB::getTablePrefix().'branch_description.branch_name as branch')
						->where('branch_description.language', $language)
						->where('branches.id', $id)
						->where(function($query) use($order_type)
						{
							$query->where('branches.delivery_type', $order_type)
									->OrWhere('branches.delivery_type', 'b');
						})
						->first();
		return $branch;
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

	public function getworkingtimes($id)
	{
		$workingtimes = DB::table('vendor_timeslot')->where('branch_id', $id)->orderby('sort_number', 'asc')->get();
		return $workingtimes;
	}

	public function getActiveBranches()
	{
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$branches = DB::table('branches')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->SelectRaw(DB::getTablePrefix().'branches.*,'.DB::getTablePrefix().'branch_description.branch_name as branch')
						->where('branch_description.language', $language)
						->where('branches.is_delete', 0)
						->where('status', 1)
						->orderby('branches.id', 'desc')
						->get();
		return $branches;
	}
}
