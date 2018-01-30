<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
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
use App\Ingredient;
use App\Execlusion;
use App\Branch;
use App\Category;
use App\Order;
use App\Deliveryboy;
use URL;
use View;
use DateTimeZone;
use DateTime;
use PHPMailer;
use App\Inotification;

use File;
use Hash;
use Redirect;
use Cart;
use Auth;
use App\Language;
use App\Vendoritem;
use stdClass;

class InotificationController extends Controller
{
    protected $delivery_method = null;
    protected $message = null;
    protected $order_status = null;
    protected $success = null;
    protected $error = null;

    public function __construct(Inotification $inotificate, Guard $auth, User $user, Order $order, Ingredient $ingredient, Branch $branch, Category $category, Deliveryboy $deliveryboy, Execlusion $execlusion)
    {
        $this->middleware('adminauth');
        $this->inotificate = $inotificate;
        $this->auth = $auth;
        $this->user = $user;
        $this->ingredient = $ingredient;
        $this->order = $order;
        $this->branch = $branch;
        $this->category = $category;
        $this->deliveryboy = $deliveryboy;
        $this->execlusion = $execlusion;
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

    public function index()
    {
        if (Session::has('userToken')){
            return View::make('members/profile');
        } else {
            return View::make('members/login');
        }
    }

    public function checkdata()
    {
        if(Input::post()){
            if (Input::post('message') != null) {
                return true;
            } else {
                return false;
            }
        } else {
            //return View::make('members/login');
            return false;
        }
    }
/*
    public function savedataAuto()
    {
        if(Input::post()){
            $email = Input::post('email');
            $sms = Input::post('sms');
            $app = Input::post('app');
            return "Email: " . $email . " and SMS: " . $sms . " and App Notification " . $app;
        } else {
            //return View::make('members/login');
            return false;
        }
    }*/

    public function notificationSend()
    {
        if ($this->checkdata() != false) {

            $delivery_method = $this->delivery_method = Input::post('delivery_method');
            $message = $this->message = Input::post('message');
            $this->error = false;

            if ($this->delivery_method == 'email') {

                $email = 'parada.ruslan90@gmail.com';
                //$msg = $this->message.",<br/>The Shuneez Team";
                //$subject = 'Notification Center';
                $this->sendmail($email, 'Notification Center', $message);
                return view('admin/notificationsend')->with('message', trans('messages.The message sent to all customers'));
            }
        } else {
            //$error = true;
            //return view('admin/notificationsend', array('data' => $data, 'error' => $error));
            return view('admin/notificationsend')->with([
                                                    'delivery_method' => $this->delivery_method,
                                                    'message' => $this->message,
                                                    'success' => $this->success,
                                                    'error' => $this->error
                                                ]);
        }

        

    }

    public function notificationSave()
    {
        if ($this->checkdata() != false) {
            $delivery_method = $this->delivery_method = Input::post('delivery_method');
            $order_status = $this->order_status = Input::post('order_status');
            $message = $this->message = Input::post('message');
            $this->error = false;
            //var_dump($data);


            $this->inotificate->save($delivery_method,$order_status,$message,$auto=1);
            $this->sandCron($delivery_method,$order_status,$message,$auto=1);
            $msg = $this->message.",<br/>The Shuneez Team";
            $subject = "Notification Center";

            $this->sendmail($email = 'parada.ruslan90@gmail.com', $subject, $msg);
        } else {
            $error = true;
        }
        //return view('admin/notificationsend', array('data' => $data, 'error' => $error));
        return view('admin/notificationauto')->with([
                                                'delivery_method' => $this->delivery_method,
                                                'order_status' => $this->order_status,
                                                'message' => $this->message,
                                                'error' => $this->error
                                            ]);

    }

    public function sandCron($delivery_method,$order_status,$message,$auto)
    {
        if ($this->delivery_method == 'email') {
            
            $msg = $this->message.",<br/>The Shuneez Team";
            $subject = "Notification Center";
            $this->sendmail($email = 'parada.ruslan90@gmail.com', $subject, $msg);
            return redirect('admin/notificationsave')->with('success', trans('messages.The message sent to customers'));
        }
    }


    public function mySendmail($email, $subject, $msg)
    {
        include('/var/www/shuneezfsc/mail/class.phpmailer.php');
        $mail = new PHPMailer(); 
        $mail->IsSMTP(); 
        $mail->SMTPAuth = true; 
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->Username = "ruslanych123@gmail.com";
        $mail->Password = "espensieonlife";
        $mail->SetFrom("ruslanych123@gmail.com");
        $mail->Subject = $subject;
        $mail->Body = $msg;
        $mail->AddAddress($email);
        $mail->Send();
        return 1;
    }

    public function sendmail($email, $subject, $msg)
    {
        //include('mail/class.phpmailer.php');
        include('/var/www/shuneezfsc/mail/class.phpmailer.php');
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


/*    public function addstaff() {
        $valid = Validator::make(Input::all(), ['branch' => 'required',
                    'name' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
                    'email' => 'required|email|unique:staffs,email|unique:adminusers,email|unique:branches,email',
                    'password' => 'required|min:6',
                    'mobile' => 'required|digits_between:9,13|unique:staffs,mobile']);
        if ($valid->fails()) {
            return redirect('admin/addstaff')->withInput(Input::all())->with('error', $valid->errors());
        } else {
            Random :
            $key = str_random(16);
            
            $key_exits = DB::table('staffs')->where('staff_key', $key)->count();
            if ($key_exits) { goto Random; }
            
            $this->staff->staff_key = $key;
            $this->staff->branch_id = Input::get('branch');
            $this->staff->name = Input::get('name');
            $this->staff->email = Input::get('email');
            $this->staff->mobile = Input::get('mobile');
            $this->staff->address = Input::get('address');
            $this->staff->status = Input::get('status');
            $this->staff->password = base64_encode(Input::get('password'));
            
            $this->staff->save();

            $name = Input::get('name');
            $email = Input::get('email');
            $password = Input::get('password');

            $msg = .",<br/>The Shuneez Team";
            $subject = "Notification Center";
            $this->sendmail($email, $subject, $msg);
            return redirect('admin/staffs')->with('success', trans('messages.Staff Add'));
        }
    }*/
}
