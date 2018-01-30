<?php namespace App\Http\Controllers\branch;

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
use PHPMailer;
use View;


class BranchauthenticateController extends Controller {

	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	    $this->flag = 0;
		$this->middleware('branchredirect', ['except' => 'getLogout']);
		$this->prefix = DB::getTablePrefix();
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$settings = DB::table('settings')->get();
		foreach ($settings as $setting) 
		{
			$config_data[$setting->setting_name] = $setting->setting_value;
		}
		View::share (['config_data'=> $config_data]);
		$this->config_data = $config_data;
	}

	public function changelanguage()
	{
		$_SESSION['language'] = (Input::get('language') != '') ? Input::get('language') : 'en';
		echo $_SESSION['language'];
		return 1;
	}
	
	public function login()
	{	
		return view('branch/login');
	}

	public function postLogin()
	{
		  $validator = Validator::make(Input::all(),
		                            ['email' => 'required',
									 'password' => 'required']
									);
									
		if($validator->fails())
		{
			
			return redirect('/branch-login')->withInput(Input::all())->with('login_error',$validator->errors());
		}
		else
		{
			$email = Input::get('email');
			$password = Input::get('password');
			$enc_password = base64_encode($password);
			$data = array("branches.email"=>$email,"branches.password"=>$enc_password); 
			$details = DB::table('branches')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->SelectRaw($this->prefix.'branches.*,'.$this->prefix.'branch_description.branch_name as branch')
						->where($data)
						->first();  
			
			if(!empty($details))
			{	
			    if($details->status == 0)
				{
				   return redirect('/branch-login')->withInput()->with('login_check', trans('messages.This branch has been blocked'));
				}
				else
				{
				     $sessn=array('branch_id' => $details->id, 'is_manager'=> 1, 'name'=>$details->branch, 'email'=>$details->email);
				     Session::put($sessn); 
				     return redirect('/branch/dashboard');
				}
				
			}
			else
			{
				return redirect('/branch-login')->withInput()->with('login_check',trans('messages.Invalid Username or Password'));
			}
		}
	}

	public function forgotpassword()
	{
		$valid = Validator::make(Input::all(),
		                         ['email' => 'required|email']
								);
		if($valid->fails())
		{
		
			$result=array("success"=>0,"msg"=>'<span class="error_msg"> '.trans("messages.Please enter valid email").'</span>');
			return json_encode($result);
		}
		else
		{
			$data = DB::table('branches')
					->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
					->SelectRaw($this->prefix.'branches.*,'.$this->prefix.'branch_description.branch_name as branch')
					->where('branches.email', Input::get('email'))
					->first(); 
			if(count($data))
			{
				$name = $data->branch;
				$email = $data->email;
				$password = base64_decode($data->password);
				$msg = "Dear ".$name.",<br><br>We have received your forgot password request. Access your account with the password below<br><br> ".$password." <br><br> Thank You,<br>Shuneez Team";
				$subject = 'Forgot Password Request - Shuneez';
				sendmail($email, $subject, $msg);
				
				$result=array("success"=>1,"msg"=>"<span class='success_msg'>Your password has been sent successfully to your email</span>");
				return json_encode($result);
			}
			else
			{
				$result=array("success"=>0,"msg"=>"<span class='error_msg'>We couldn't find your account with that information</span>");
				return json_encode($result);
			}
		}
	}
	
	public function stafflogin_form()
	{	
		return view('branch/stafflogin');
	}

	public function stafflogin()
	{ 
		  $validator = Validator::make(Input::all(),
		                            ['email' => 'required',
									 'password' => 'required']
									);
									
		if($validator->fails())
		{
			
			return redirect('/staff-login')->withInput(Input::all())->with('login_error',$validator->errors());
		}
		else
		{
			$email = Input::get('email');
			$password = Input::get('password');
			$enc_password = base64_encode($password);
			$data = array("staffs.email"=>$email,"staffs.password"=>$enc_password); 
			$details = DB::table('staffs')
						->join('branch_description', 'staffs.branch_id', '=', 'branch_description.branch_id')
						->SelectRaw($this->prefix.'staffs.*,'.$this->prefix.'branch_description.branch_name')
						->where($data)
						->first();  
			
			if(!empty($details))
			{	
			    if($details->status == 0)
				{
				   return redirect('/staff-login')->withInput()->with('login_check', 'This user has been blocked');
				}
				else
				{
				     $sessn=array('branch_id' => $details->branch_id, 'staff_id' => $details->id, 'is_manager'=> 0, 'name'=>$details->branch_name, 'staff_name'=>$details->name, 'email'=>$details->email);
				     Session::put($sessn); 
				     return redirect('/branch/dashboard');
				}
				
			}
			else
			{
				return redirect('/staff-login')->withInput()->with('login_check','Invalid Username or Password');
			}
		}
	}

	public function staff_forgotpassword()
	{
		$valid = Validator::make(Input::all(),
		                         ['email' => 'required|email']
								);
		if($valid->fails())
		{
		
			$result=array("success"=>0,"msg"=>'<span class="error_msg"> '.trans("messages.Please enter valid email").'</span>');
			return json_encode($result);
		}
		else
		{
			$data = DB::table('staffs')->where('email', Input::get('email'))->first();
			if(count($data))
			{
				$name = $data->branch;
				$email = $data->email;
				$password = base64_decode($data->password);
				$msg = "Dear ".$name.",<br><br>We have received your forgot password request. Access your account with the password below<br><br> ".$password." <br><br> Thank You,<br>Shuneez Team";
				$subject = 'Forgot Password Request - Shuneez';
				sendmail($email, $subject, $msg);
				
				$result=array("success"=>1,"msg"=>"<span class='success_msg'>Your password has been sent successfully to your email</span>");
				return json_encode($result);
			}
			else
			{
				$result=array("success"=>0,"msg"=>"<span class='error_msg'>We couldn't find your account with that information</span>");
				return json_encode($result);
			}
		}
	}

}
