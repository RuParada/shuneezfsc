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
use View;
use Redirect;
use URL;

class LanguageController extends Controller {

	public function __construct(Guard $auth)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	}
	
	public function getbackend_languages()
    {
			$myfile = fopen(base_path()."/resources/lang/".$this->current_language."/messages.php", "r") or die("Unable to open file!");
			$msg = fread($myfile,filesize(base_path()."/resources/lang/".$this->current_language."/messages.php"));
			fclose($myfile);
			
			return view('admin/backend_languages', array('msg' => $msg));
    }
    
    public function update_backend_languages()
    {
		$myfile = fopen(base_path()."/resources/lang/".$this->current_language."/messages.php", "w") or die("Unable to open file!");
		fwrite($myfile, Input::get('message'));
		fclose($myfile);
		return redirect('admin/backend_languages')->with('success', trans('messages.Updated Successfully'));
	}
	
	public function getfrontend_languages()
    {
			$myfile = fopen(base_path()."/resources/lang/".$this->current_language."/frontend.php", "r") or die("Unable to open file!");
			$msg = fread($myfile,filesize(base_path()."/resources/lang/".$this->current_language."/messages.php"));
			fclose($myfile);
			
			return view('admin/frontend_languages', array('msg' => $msg));
    }
    
    public function update_frontend_languages()
    {
		$myfile = fopen(base_path()."/resources/lang/".$this->current_language."/frontend.php", "w") or die("Unable to open file!");
		fwrite($myfile, Input::get('message'));
		fclose($myfile);
		return redirect('admin/frontend_languages')->with('success', trans('messages.Updated Successfully'));
	}
	
	public function getapi_languages()
    {
			$myfile = fopen(base_path()."/resources/lang/".$this->current_language."/api.php", "r") or die("Unable to open file!");
			$msg = fread($myfile,filesize(base_path()."/resources/lang/".$this->current_language."/api.php"));
			fclose($myfile);
			
			return view('admin/api_languages', array('msg' => $msg));
    }
    
    public function update_api_languages()
    {
		$myfile = fopen(base_path()."/resources/lang/".$this->current_language."/api.php", "w") or die("Unable to open file!");
		fwrite($myfile, Input::get('message'));
		fclose($myfile);
		return redirect('admin/api_languages')->with('success', trans('messages.Updated Successfully'));
	}
}
