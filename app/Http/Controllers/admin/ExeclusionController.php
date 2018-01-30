<?php namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Validator;
use Input;
use DB;
use Response;
use Mail;
use Session;
use App\Execlusion;
use App\Language;
use View;

class ExeclusionController extends Controller {

	public function __construct(Guard $auth, Execlusion $execlusion, Language $language)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->execlusion = $execlusion;
	    $this->language = $language;
		$this->prefix = DB::getTablePrefix();
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	}
	
	/*********** Get Categories ***************/
	
	public function getexeclusions()
	{
		$execlusions = $this->execlusion->getexeclusions();

		//print_r($execlusions);exit;				 
		return view('admin/execlusions', array('execlusions' => $execlusions));
	}
	
	/*********** Add Execlusion Form *******************/
	
	public function addexeclusion_form()
	{
		$languages = $this->language->getlanguages();
		return view('admin/addexeclusion', array('languages' => $languages));
	}
	
	/************* Insert Execlusion *******************/
	
	Public function addexeclusion()
	{
		$execlusions = Input::get('execlusion_name');
		$valid = Validator::make(Input::all(),
								['status' => 'required']);
		$array_valid = $this->execlusion->rules($execlusions);
		if($array_valid['error_count'])
		{
			return redirect('admin/addexeclusion')->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
								
		if($valid->fails())
		{
			return redirect('admin/addexeclusion')->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{ 
		    $this->execlusion->status = Input::get('status');
			$this->execlusion->save();
			
			$execlusion_id = $this->execlusion->id;
			$language = Input::get('language');
			$execlusion_name = Input::get('execlusion_name');
			if(count($execlusion_name) > 0)
			{
				for($i=0; $i< count($execlusion_name); $i++)
				{ 
					DB::table('execlusion_description')->insert(['execlusion_id' => $execlusion_id, 'execlusion_name' => $execlusion_name[$i], 'language' => $language[$i]]);
				}
			}
			
			return redirect('admin/execlusions')->with('success', trans('messages.Execlusions Add'));
		}	
	}
	
	/*************** Get Execlusion *********************/
	
	public function getexeclusion($id)
	{
		$languages = $this->language->getlanguages();
		$execlusion = DB::table('execlusions')->where('id', $id)->first();
		
		return view('admin/editexeclusion', array('languages' => $languages, 'execlusion' => $execlusion));
	}
	
	/************* Update Execlusion *******************/
	
	Public function updateexeclusion()
	{
		$execlusion_id = Input::get('id');
		$execlusions = Input::get('execlusion_name');
		$valid = Validator::make(Input::all(),
								['status' => 'required']);
		$array_valid = $this->execlusion->rules($execlusions);
		if($array_valid['error_count'])
		{
			return redirect('admin/editexeclusion/'.$execlusion_id)->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
								
		if($valid->fails())
		{
			return redirect('admin/editexeclusion/'.$execlusion_id)->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{ 
		    $this->execlusion->status = Input::get('status');
			
			DB::table('execlusions')->where('id', $execlusion_id)->update($this->execlusion['attributes']);
			
			$language = Input::get('language');
			$execlusion_name = Input::get('execlusion_name');
			DB::table('execlusion_description')->where('execlusion_id', $execlusion_id)->delete();
			if(count($execlusion_name) > 0)
			{
				for($i=0; $i< count($execlusion_name); $i++)
				{ 
					DB::table('execlusion_description')->insert(['execlusion_id' => $execlusion_id, 'execlusion_name' => $execlusion_name[$i], 'language' => $language[$i]]);
				}
			}
			
			return redirect('admin/execlusions')->with('success', trans('messages.Execlusions Update'));
		}			
	}
	
	/**********Update Execlusion Status************/

	public function change_execlusionstatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('execlusions')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => trans('messages.Status Change'));
		return json_encode($result);
	}
	
	/*********** Filter Execlusions ***************/
	
	public function filterexeclusion()
	{
		$execlusion = Input::get('name');
		$status = Input::get('status');
		
		$execlusions = DB::table('execlusions')
						->join('execlusion_description', 'execlusions.id', '=', 'execlusion_description.execlusion_id')
						->SelectRaw($this->prefix.'execlusions.*,'.$this->prefix.'execlusion_description.execlusion_name as execlusion')
						->where(function($query) use($execlusion, $status)
						{
							if($execlusion != '')
							{
								$query->where('execlusion_description.execlusion_name', 'like', '%'.$execlusion.'%');
							}
							if($status != '')
							{
								$query->where('execlusions.status', $status);
							}
						})
						->where('execlusion_description.language', $this->current_language)
						->paginate(10);
						 
		return view('admin/execlusions', array('execlusions' => $execlusions));
	}
	
	/************* Delete Execlusion ***************/
	
	public function deleteexeclusion($id)
	{
		DB::table('vendor_item_execlusions')->where('execlusion_id', $id)->delete();
		DB::table('execlusions')->where('id', $id)->delete();
		return redirect('admin/execlusions')->with('success', trans('messages.Execlusions Delete'));
	}

}
