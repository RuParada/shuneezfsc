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
use PHPMailer;
use View;

class AdminauthenticateController extends Controller {

	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	    $this->flag = 0;
		$this->middleware('adminredirect', ['except' => 'getLogout']);
		$this->prefix = DB::getTablePrefix();
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$settings = DB::table('settings')->get();
	    foreach ($settings as $setting) 
		{
			$config_data[$setting->setting_name] = $setting->setting_value;
		}
		
		$this->config_data = $config_data;
		View::share (['config_data'=> $config_data, 'default_currency' => getdefault_currency()]);
	}

	public function changelanguage()
	{
		$_SESSION['language'] = (Input::get('language') != '') ? Input::get('language') : 'en';
		echo $_SESSION['language'];
		return 1;
	}
	
	public function login()
	{	
		return view('admin/login');
	}

	public function postLogin()
	{
		  $validator = Validator::make(Input::all(),
		                            ['username' => 'required',
									 'password' => 'required']
									);
									
		if($validator->fails())
		{
			
			return redirect('/admin')->withInput(Input::all())->with('login_error',$validator->errors());
		}
		else
		{
			$username = Input::get('username');
			$password = Input::get('password');
			$enc_password=base64_encode($password);
			$data=array("username"=>$username,"password"=>$enc_password); 
			$details = [];
			$admin = DB::table('adminusers')->where($data)->first();  
			if(!empty($admin))
			{
				$details = $admin;
			}
			else
			{
				$data=array("email"=>$username,"password"=>$enc_password); 
				$branch = DB::table('branches')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->SelectRaw($this->prefix.'branches.*,'.$this->prefix.'branch_description.branch_name as branch')
						->where($data)
						->first(); 
				if(!empty($branch))
				{
					$details = $branch;
				}
				else
				{ 
					$staff = DB::table('staffs')
							->join('branch_description', 'staffs.branch_id', '=', 'branch_description.branch_id')
							->SelectRaw($this->prefix.'staffs.*,'.$this->prefix.'branch_description.branch_name')
							->where($data)
							->first();  
					if(!empty($staff))
					{
						$details = $staff;
					}
				}  
			}
			
			
			
			if(!empty($details))
			{	
			    if($details->status==0)
				{
				   return redirect('/admin')->withInput()->with('login_check','This user has been blocked');
				}
				else
				{
					 if(count($admin))
					 {
						$sessn=array('admin_userid' => $details->id, 'is_admin'=>$details->superadmin,'name'=>$details->name,'username'=>$details->username,'email'=>$details->email, 'add' => $details->add_privilege, 'edit' => $details->edit_privilege, 'delete' => $details->delete_privilege);
						Session::put($sessn); 
						return redirect('/admin/dashboard');
					 }
					 elseif(count($branch))
					 {
						 $sessn=array('branch_id' => $details->id, 'is_manager'=> 1, 'name'=>$details->branch, 'email'=>$details->email);
						 Session::put($sessn); 
						 return redirect('/branch/dashboard');
					 }
					 else
					 {
						 $sessn=array('branch_id' => $details->branch_id, 'staff_id' => $details->id, 'is_manager'=> 0, 'name'=>$details->branch_name, 'staff_name'=>$details->name, 'email'=>$details->email);
						 Session::put($sessn); 
						 return redirect('/branch/dashboard');
					 }
				     
				}
				
			}
			else
			{
				return redirect('/admin')->withInput()->with('login_check','Invalid Username or Password');
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
		
			$result=array("success"=>0,"msg"=>'<span class="error_msg">Please enter valid email...</span>');
			return json_encode($result);
		}
		else
		{
			$data = [];
			$admin = DB::table('adminusers')->where('email', Input::get('email'))->first();
			if(count($admin))
			{ 
				$data = $admin;
				$username = $data->username;
			}
			else
			{
				$branch = DB::table('branches')
					->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
					->SelectRaw($this->prefix.'branches.*,'.$this->prefix.'branch_description.branch_name as branch')
					->where('branches.email', Input::get('email'))
					->first();
				if(count($branch))
				{ 
					$data = $branch;
					$username = $data->branch;
				}
				else
				{
					$staff = DB::table('staffs')->where('email', Input::get('email'))->first();
					if(count($staff))
					{ 
						$data = $staff;
						$username = $data->name;
					}
				}
			}  
			if(count($data))
			{
				$email = $data->email;
				$password = base64_decode($data->password);
				$msg = "Dear ".$username.",<br><br>We have received your forgot password request. Access your account with the password below<br><br> ".$password." <br><br> Thank You,<br>Shuneez Team";
				$subject = 'Forgot Password Request - Shuneez';
				$this->sendmail($email, $subject, $msg);
				
				$result=array("success"=>1,"msg"=>"<span class='success_msg' style='color:green'>Your password has been sent successfully to your email</span>");
				return json_encode($result);
			}
			else
			{
				$result=array("success"=>0,"msg"=>"<span class='error_msg'>We couldn't find your account with that information</span>");
				return json_encode($result);
			}
		}
	}
	
	public function sendmail($email, $subject, $msg)
	{
		include('mail/class.phpmailer.php');
		
		$mail = new PHPMailer(); // create a new object
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465; // or 587
		$mail->IsHTML(true);
		$mail->Username = "order@shuneezfsc.com";
		$mail->Password = "shuneez123";
		$mail->SetFrom("order@shuneezfsc.com");
		$mail->Subject = $subject;
		$mail->Body = $msg;
		$mail->AddAddress($email);
		$mail->Send();
		
		return 1;
	}

}
