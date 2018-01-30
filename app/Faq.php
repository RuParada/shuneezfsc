<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Faq extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;
	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'faq';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['order', 'status', 'created_by', 'updated_by'];
	
	

	public function getfaqs()
	{
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$faqs = DB::table('faq')
						->join('faq_description', 'faq.id', '=', 'faq_description.faq_id')
						->SelectRaw(DB::getTablePrefix().'faq.*,'.DB::getTablePrefix().'faq_description.question, '.DB::getTablePrefix().'faq_description.answer')
						->where('faq_description.language', $language)
						->paginate(10);
						 
		return $faqs;
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
