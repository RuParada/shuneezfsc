<?php

namespace App\Http\Controllers\branch;

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
use App\Deliveryboy;
use App\Branch;
use App\Language;
use App\Ingredient;

class DeliveryboyController extends Controller {

    public function __construct(Guard $auth, Deliveryboy $deliveryboy, Language $language, Branch $branch, Ingredient $ingredient) {
        $this->middleware('branchauth');
        $this->auth = $auth;
        $this->deliveryboy = $deliveryboy;
        $this->branch = $branch;
        $this->language = $language;
        $this->ingredient = $ingredient;
        $this->prefix = DB::getTablePrefix();
        $this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
    }

    /*     * ******** Get deliveryboys *********** */

    public function getdeliveryboys() {
        $deliveryboys = $this->deliveryboy->getbranch_alldeliveryboys(Session('branch_id'));
        return view('branch/deliveryboys', array('deliveryboys' => $deliveryboys));
    }

    public function adddeliveryboy_form() {
		$languages = $this->language->getlanguages();
		return view('branch/adddeliveryboy', array('languages' => $languages));
    }

    public function adddeliveryboy() {
		$deliveryboys = Input::get('name');
        $valid = Validator::make(Input::all(), 
					['branch' => 'required',
					'email' => 'email|unique:deliveryboys,email',
                    'password' => 'required|min:5',
                    'mobile' => 'required|numeric|unique:deliveryboys,mobile',
                    'image' => 'mimes:jpeg,jpg,png',]);
        $array_valid = $this->deliveryboy->rules($deliveryboys);
		if($array_valid['error_count'])
		{
			return redirect('branch/adddeliveryboy')->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
        if ($valid->fails()) {
            return redirect('branch/adddeliveryboy')->withInput(Input::all())->with('error', $valid->errors());
        } else {
			Random :
			$key = str_random(16);
			
			$key_exits = DB::table('deliveryboys')->where('deliveryboy_key', $key)->count();
			if ($key_exits) { goto Random; }
			
			$this->deliveryboy->deliveryboy_key = $key;
            $this->deliveryboy->branch_id = Input::get('branch');
            $this->deliveryboy->address = Input::get('address');
            $this->deliveryboy->email = Input::get('email');
            $this->deliveryboy->mobile = Input::get('mobile');
            $this->deliveryboy->status = Input::get('status');
            $this->deliveryboy->password = base64_encode(Input::get('password'));
            $this->deliveryboy->created_by = Session('branch_id');
			
			if(Input::file('image') != '')
			{
				$logo = str_random(6).Input::file('image')->getClientOriginalName();
				$dest = 'assets/uploads/deliveryboys';
				Input::file('image')->move($dest,$logo);
				$this->deliveryboy->image = $logo;
			}
            
            $this->deliveryboy->save();

            $name = Input::get('name');
            $email = Input::get('email');
            $password = Input::get('password');

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
              
            $deliveryboy_id = $this->deliveryboy->id;
			$name = Input::get('name');
			$language = Input::get('language');
			if(count($name) > 0)
			{
				for($i=0; $i<count($name); $i++)
				{
					DB::table('deliveryboy_description')->insert(['deliveryboy_id' => $deliveryboy_id, 'deliveryboy_name' => $name[$i], 'language' => $language[$i]]);
				}
			}
            return redirect('branch/deliveryboys')->with('success', trans('messages.Deliveryboy Add'));
        }
    }

    /*     * ********Update deliveryboy Status*********** */

    public function change_deliveryboystatus() {
        $id = Input::get('id');
        $status = (Input::get('status') == 0) ? 1 : 0;
        DB::table('deliveryboys')->where('id', $id)->update(['status' => $status]);
        $result = array('success' => 1, 'msg' => trans('messages.Status Change'));
        return json_encode($result);
    }

    /*     * *******Get deliveryboy********* */

    public function getdeliveryboy($id) {
		$languages = $this->language->getlanguages();
        $deliveryboy = DB::table('deliveryboys')
						->join('branch_description', 'deliveryboys.branch_id', '=', 'branch_description.branch_id')
						->SelectRaw($this->prefix.'deliveryboys.*,'.$this->prefix.'branch_description.branch_name as branch')
						->where('deliveryboys.id', $id)
						->first();
		return view('branch/editdeliveryboy', array('deliveryboy' => $deliveryboy, 'languages' => $languages));
    }

    /*     * *******Update deliveryboy********* */

    public function updatedeliveryboy() {
		$deliveryboys = Input::get('name');
        $id = Input::get('id');
        $valid = Validator::make(Input::all(), ['branch' => 'required',
											'name' => 'required',
											'email' => 'email|unique:deliveryboys,email,' . $id,
											'mobile' => 'required|numeric|unique:deliveryboys,mobile,' . $id,
											'password' => 'min:6',
											]);
		$array_valid = $this->deliveryboy->rules($deliveryboys);
		if($array_valid['error_count'])
		{
			return redirect('branch/editdeliveryboy/' . $id)->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
        if ($valid->fails()) 
        {
            return redirect('branch/editdeliveryboy/' . $id)->withInput(Input::all())->with('error', $valid->errors());
        } 
        else 
        {
			$this->deliveryboy->branch_id = Input::get('branch');
            $this->deliveryboy->address = Input::get('address');
            $this->deliveryboy->email = Input::get('email');
            $this->deliveryboy->mobile = Input::get('mobile');
            $this->deliveryboy->status = Input::get('status');
            $this->deliveryboy->password = base64_encode(Input::get('password'));
            $this->deliveryboy->updated_by = Session('admin_userid');

            DB::table('deliveryboys')->where('id', $id)->update($this->deliveryboy['attributes']);
            
            $name = Input::get('name');
			$language = Input::get('language');
			DB::table('deliveryboy_description')->where('deliveryboy_id', $id)->delete();
			if(count($name) > 0)
			{
				for($i=0; $i<count($name); $i++)
				{
					DB::table('deliveryboy_description')->insert(['deliveryboy_id' => $id, 'deliveryboy_name' => $name[$i], 'language' => $language[$i]]);
				}
			}
			
            return redirect('branch/deliveryboys')->with('success', trans('messages.Deliveryboy Update'));
        }
    }

    /*     * *******Delete deliveryboy********* */

    public function deletedeliveryboy($id) {
        $user = DB::table('deliveryboys')->where('id', $id)->first();
        DB::table('deliveryboys')->where('id', $id)->update(['is_delete' => 1]);
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
        return redirect('branch/deliveryboys')->with('success', trans('messages.Deliveryboy Delete'));
    }

    /*     * *******Restore deliveryboy********* */

    public function restoredeliveryboy($id) {
        $deliveryboy = DB::table('deliveryboys')->where('id', $id)->first();
        DB::table('deliveryboys')->where('id', $id)->update(['is_delete' => 0]);
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
        return redirect('branch/deliveryboys')->with('success', trans('messages.Deliveryboy Restore'));
    }

    /*     * ********Filter deliveryboy*********** */

    public function filterdeliveryboys() {
        $search = Input::get('name');
        $status = Input::get('status');

        $deliveryboys = DB::table('deliveryboys')
						->join('deliveryboy_description', 'deliveryboys.id', '=', 'deliveryboy_description.deliveryboy_id')
						->join('branch_description', 'deliveryboys.branch_id', '=', 'branch_description.branch_id')
						->SelectRaw(DB::getTablePrefix().'deliveryboys.*,'.DB::getTablePrefix().'deliveryboy_description.deliveryboy_name as name,'.DB::getTablePrefix().'branch_description.branch_name as branch')
						->where(function($query) use($search) {
							if ($search != '') {
								$query->where('deliveryboy_description.deliveryboy_name', 'like', '%' . $search . '%')
								->OrWhere('deliveryboys.mobile', 'like', '%' . $search . '%')
								->OrWhere('deliveryboys.email', 'like', '%' . $search . '%');
							}
						})
						->where(function($qry) use($status) {
							if ($status != '') {
								if ($status == 'deleted') {
									$qry->where('deliveryboys.is_delete', 1);
								} else {
									$qry->where('deliveryboys.status', $status)->where('deliveryboys.is_delete', 0);
								}
							}
							elseif ($status == '') {
								$qry->where('deliveryboys.is_delete', 0);
							}
						})
						->where('deliveryboys.branch_id', Session('branch_id'))
						->where('deliveryboy_description.language', $this->current_language)
						->where('branch_description.language', $this->current_language)
						->orderby('deliveryboys.id', 'desc')
						->paginate(10);
		return view('branch/deliveryboys', array('deliveryboys' => $deliveryboys));
    }
    
   
}
