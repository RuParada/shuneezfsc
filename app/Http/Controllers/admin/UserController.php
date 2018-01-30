<?php

namespace App\Http\Controllers\admin;

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
use App\User;
use DateTimeZone;
use DateTime;
use PHPMailer;

class UserController extends Controller {

    public function __construct(Guard $auth, User $user) {
        $this->middleware('adminauth');
        $this->auth = $auth;
        $this->user = $user;
        $this->flag = 0;
    }

    /*     * ******** Get Users *********** */

    public function getusers() {
        $users = $this->user->getusers();
		return view('admin/users', array('users' => $users));
    }

    public function adduser_form() {
       return view('admin/adduser');
    }

    public function adduser() {
        $valid = Validator::make(Input::all(), ['first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|min:6',
                    'mobile' => 'required|digits_between:9,13|unique:users,mobile']);
        if ($valid->fails()) {
            return redirect('admin/adduser')->withInput(Input::all())->with('error', $valid->errors());
        } else {
			Random :
			$key = str_random(16);
			
			$key_exits = DB::table('users')->where('customer_key', $key)->count();
			if ($key_exits) { goto Random; }
			
			$this->user->customer_key = $key;
            $this->user->first_name = Input::get('first_name');
            $this->user->last_name = Input::get('last_name');
            $this->user->email = Input::get('email');
            $this->user->mobile = Input::get('mobile');
            $this->user->status = Input::get('status');
            $this->user->verify = 1;
            $this->user->password = bcrypt(Input::get('password'));
            
            $this->user->save();

            $name = Input::get('first_name') . ' ' . Input::get('last_name');
            $email = Input::get('email');
            $password = Input::get('password');
            
            $email = Input::get('email');
			$name = Input::get('first_name').' '.Input::get('last_name');
			$pwd = Input::get('password');
			
			$msg = "Hello ".$name.",<br><br>Your account has been created successfully.<br><br> You can find Your credentials below: <br>Username: ".$email."<br>Password: ".$pwd." <br> Thank You,<br>The Shuneez Team";
			$subject = "The Shunnez Registration";
			$this->sendmail($email, $subject, $msg);

			/* $this->SendSMS($message, $mobile);

              Mail::send([],
              array('pass' => $password,'email' => $email,'name' => $name), function($message) use ($password,$email,$name)
              {
              $mail_body = "Hello {name},<br><br>You account has been succesfully created by admin. <br><br> You can find your credentials below: <br>Username: {email}<br>Password: {password} <br><br> Thank You,<br>The DoctorWeb Team";
              $mail_body = str_replace("{password}", $password, $mail_body);
              $mail_body = str_replace("{email}", $email, $mail_body);
              $mail_body = str_replace("{name}", $name, $mail_body);
              $message->setBody($mail_body, 'text/html');
              $message->to($email);
              $message->subject('DoctorWeb Registration');
              }); */
            return redirect('admin/users')->with('success', trans('messages.User Add'));
        }
    }

    /*     * ********Update User Status*********** */

    public function change_userstatus() {
        $id = Input::get('id');
        $status = (Input::get('status') == 0) ? 1 : 0;
        DB::table('users')->where('id', $id)->update(['status' => $status]);
        $result = array('success' => 1, 'msg' => trans('messages.Status Change'));
        return json_encode($result);
    }

    /*     * *******Get User********* */

    public function getuser($id) {
        $user = DB::table('users')->where('id', $id)->first();
        return view('admin/edituser', array('user' => $user));
    }

    /*     * *******Update User********* */

    public function updateuser() {
        $id = Input::get('id');
        $valid = Validator::make(Input::all(), ['first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $id,
                    'mobile' => 'required|digits_between:9,13|unique:users,mobile,' . $id,
                    'password' => 'min:6',
                    ]);
        if ($valid->fails()) {
            return redirect('admin/getuser/' . $id)->withInput(Input::all())->with('error', $valid->errors());
        } else {
            $this->user->first_name = Input::get('first_name');
            $this->user->last_name = Input::get('last_name');
            $this->user->email = Input::get('email');
            $this->user->mobile = Input::get('mobile');
            $this->user->status = Input::get('status');

            DB::table('users')->where('id', $id)->update($this->user['attributes']);
            return redirect('admin/users')->with('success', trans('messages.User Update'));
        }
    }

    /*     * *******Delete User********* */

    public function deleteuser($id) {
        $user = DB::table('users')->where('id', $id)->first();
        DB::table('users')->where('id', $id)->update(['is_delete' => 1]);
        /* $name = $user->name;
          $email = $user->email;
          Mail::send([],
          array('email' => $email,'name' => $name), function($message) use ($email,$name)
          {
          $mail_body = "Hello {name},<br><br>You account has been deleted by admin. <br><br> Thank You,<br>The DoctorWeb Team";
          $mail_body = str_replace("{name}", $name, $mail_body);
          $message->setBody($mail_body, 'text/html');
          $message->to($email);
          $message->subject('DoctorWeb Account Delete');
          }); */
        return redirect('admin/users')->with('success', trans('messages.User Delete'));
    }

    /*     * *******Restore User********* */

    public function restoreuser($id) {
        $user = DB::table('users')->where('id', $id)->first();
        DB::table('users')->where('id', $id)->update(['is_delete' => 0]);
        /* $name = $user->name;
          $email = $user->email;
          Mail::send([],
          array('email' => $email,'name' => $name), function($message) use ($email,$name)
          {
          $mail_body = "Hello {name},<br><br>You account has been deleted by admin. <br><br> Thank You,<br>The DoctorWeb Team";
          $mail_body = str_replace("{name}", $name, $mail_body);
          $message->setBody($mail_body, 'text/html');
          $message->to($email);
          $message->subject('DoctorWeb Account Delete');
          }); */
        return redirect('admin/users')->with('success', trans('messages.User Restore'));
    }

    /*     * ********Filter Users*********** */

    public function filterusers() {
        $search = Input::get('name');
        $status = Input::get('status');

        $users = DB::table('users')
				->where(function($query) use($search, $status) {
                    if ($search != '') {
                        $query->where('first_name', 'like', '%' . $search . '%')
                        ->OrWhere('last_name', 'like', '%' . $search . '%')
                        ->OrWhere('mobile', 'like', '%' . $search . '%')
                        ->OrWhere('email', 'like', '%' . $search . '%');
                    } elseif ($status == '') {
                        $query->where('is_delete', 0);
                    }
                })
                ->where(function($qry) use($status) {
                    if ($status != '') {
                        if ($status == 'deleted') {
                            $qry->where('is_delete', 1);
                        } else {
                            $qry->where('status', $status)->where('is_delete', 0);
                        }
                    }
                })
                ->orderby('id', 'desc')
                ->paginate(10);

        return view('admin/users', array('users' => $users));
    }
    
    public function sendnewsletter_form()
    {
        return view('admin/sendnewsletter');
    }
    
    public function sendnewsletter()
    {
        $valid = Validator::make(Input::all(),
                                        ['subject' => 'required',
                                         'description' => 'required',
                                        ]);
        if($valid->fails())
        {
                return redirect('admin/sendnewsletter')->withInput(Input::all())->with('error', $valid->errors());
        }
        else
        {
            $subscribers = DB::table('newsletter_subscribers')->get();
            if(count($subscribers) > 0)
            {
                $a = 1;
            }

            return redirect('admin/sendnewsletter')->with('success', trans('messages.Newsletter send successfully'));
        }
    }
    
    public function subscribers()
    {
        $subscribers = DB::table('newsletter_subscribers')->orderby('id', 'desc')->paginate(10);
        return view('admin/subscribers', array('subscribers' => $subscribers));
    }
    
    public function filtersubscribers()
    {
        $email = Input::get('name');
        $subscribers = DB::table('newsletter_subscribers')
                        ->where(function($query) use($email)
                        {
                            $query->where('email', 'like', '%'.$email.'%');
                        })
                        ->orderby('id', 'desc')->paginate(10);
        return view('admin/subscribers', array('subscribers' => $subscribers));
    }
    
    public function deletesubscriber($id)
    {
        DB::table('newsletter_subscribers')->where('id', $id)->delete();
        return redirect('admin/subscribers')->with('success', trans('messages.Newsletter deleted successfully'));
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
		$mail->Username = "shuneezfood@gmail.com";
		$mail->Password = "shuneez123";
		$mail->SetFrom("shuneezfood@gmail.com");
		$mail->Subject = $subject;
		$mail->Body = $msg;
		$mail->AddAddress($email);
		$mail->Send();
		
		return 1;
	}
}
