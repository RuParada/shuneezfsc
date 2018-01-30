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
use App\Deliveryboy;
use App\Branch;
use App\Language;
use App\Ingredient;
use Redirect;

class DeliveryboyController extends Controller {

    public function __construct(Guard $auth, Deliveryboy $deliveryboy, Language $language, Branch $branch, Ingredient $ingredient) {
        $this->middleware('adminauth');
        $this->auth = $auth;
        $this->deliveryboy = $deliveryboy;
        $this->branch = $branch;
        $this->language = $language;
        $this->ingredient = $ingredient;
        $this->prefix = DB::getTablePrefix();
        $this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
        $settings = DB::table('settings')->get();
        foreach ($settings as $setting) 
        {
          $config_data[$setting->setting_name] = $setting->setting_value;
        }
        
        $this->config_data = $config_data;
    }

    /*     * ******** Get deliveryboys *********** */

    public function getdeliveryboys() {
        $deliveryboys = $this->deliveryboy->getdelivery_boys();
        $branches = $this->branch->getbranches();
		return view('admin/deliveryboys', array('deliveryboys' => $deliveryboys, 'branches' => $branches));
    }

    public function adddeliveryboy_form() {
  		$languages = $this->language->getlanguages();
  		$branches = $this->branch->getbranches();
      $countries = getDookCounties();
  		return view('admin/adddeliveryboy', array('languages' => $languages,'branches' => $branches, 'countries' => $countries));
    }

    public function adddeliveryboy() 
    {
		  $deliveryboys = Input::get('name');
      $valid = Validator::make(Input::all(), 
          					['branch' => 'required',
          					'email' => 'email|unique:deliveryboys,email',
                    'password' => 'required|min:5',
                    'mobile' => 'required|numeric|unique:deliveryboys,mobile',
                    'image' => 'mimes:jpeg,jpg,png',
                    'country' => 'required',
                    'city' => 'required',
                    'team_id' => 'required',
                    'vehicle_type' => 'required',
                    'vehicle_attribute' => 'required',
                    'commission_percent' => 'required',
                    'type' => 'required']);
        $array_valid = $this->deliveryboy->rules($deliveryboys);
    		if($array_valid['error_count'])
    		{
    			return redirect('admin/adddeliveryboy')->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
    		}
        if ($valid->fails()) {
          //print_r($valid->errors()); exit;
            return redirect('admin/adddeliveryboy')->withInput(Input::all())->with('error', $valid->errors());
        } else {
          
            $fields = ['firstName' => Input::get('name')[0],
                       'lastName' => '',
                       'name' => Input::get('name')[0],
                       'phone' => Input::get('mobile'),
                       'vehicleType' => Input::get('vehicle_type'),
                       'vehicleAttribute' => Input::get('vehicle_attribute'),
                       'driverCommissionPercent' => Input::get('commission_percent'),
                       'email' => Input::get('email'),
                       'password' => Input::get('password'),
                       'countryId' => Input::get('country'),
                       'cityId' => Input::get('city'),
                       'companyId' => $this->config_data['dook_company_id']];
            $fields = json_encode($fields);

            $header = array();
            $header[] = 'Content-type: application/json; charset=utf-8';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://test-api.dook.sa/api/Teams/".Input::get('team_id')."/drivers?access_token=".$this->config_data['dook_access_token']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response);
            //echo '<pre>'; print_r($data); exit;
            if ( !isset($data->id) ) {
              return Redirect::back()->withInput(Input::all())->with('dook_error', $data->error->message);
            }
          
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
            $this->deliveryboy->created_by = Session('admin_userid');
            $this->deliveryboy->type = Input::get('type');
            $this->deliveryboy->dook_id = ( Input::get('type') == 1 ) ? $data->id : '';
            $this->deliveryboy->vehicle_type = Input::get('vehicle_type');
            $this->deliveryboy->vehicle_attribute = Input::get('vehicle_attribute');
            $this->deliveryboy->commission_percent = Input::get('commission_percent');
            $this->deliveryboy->country_id = Input::get('country');
            $this->deliveryboy->city_id = Input::get('city');
            $this->deliveryboy->team_dook_id = Input::get('team_id');

			
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
            return redirect('admin/deliveryboys')->with('success', trans('messages.Deliveryboy Add'));
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
            ->join('dook_teams', 'deliveryboys.team_dook_id', '=', 'dook_teams.dook_id')
						->SelectRaw($this->prefix.'deliveryboys.*,'.$this->prefix.'branch_description.branch_name as branch, '.$this->prefix.'dook_teams.name as team')
						->where('deliveryboys.id', $id)
						->first();
    		$branches = $this->branch->getbranches();
        $countries = getDookCounties();
        $cities = getDookCities($deliveryboy->country_id);
        return view('admin/editdeliveryboy', array('deliveryboy' => $deliveryboy, 'languages' => $languages, 'branches' => $branches, 'countries' => $countries, 'cities' => $cities));
    }

    /*     * *******Update deliveryboy********* */

    public function updatedeliveryboy() {
		$deliveryboys = Input::get('name');
        $id = Input::get('id');
        $valid = Validator::make(Input::all(), ['branch' => 'required',
											'name' => 'required',
											'email' => 'email|unique:deliveryboys,email,' . $id,
											'mobile' => 'required|numeric|unique:deliveryboys,mobile,' . $id,
											'password' => 'min:5',
											]);
		$array_valid = $this->deliveryboy->rules($deliveryboys);
		if($array_valid['error_count'])
		{
			return redirect('admin/editdeliveryboy/' . $id)->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
        if ($valid->fails()) 
        {
            return redirect('admin/editdeliveryboy/' . $id)->withInput(Input::all())->with('error', $valid->errors());
        } 
        else 
        {
			$this->deliveryboy->branch_id = Input::get('branch');
            $this->deliveryboy->address = Input::get('address');
            $this->deliveryboy->email = Input::get('email');
            $this->deliveryboy->mobile = Input::get('mobile');
            $this->deliveryboy->status = Input::get('status');
            if(Input::get('password') != '')
            {
              $this->deliveryboy->password = base64_encode(Input::get('password'));
            }    
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
			
            return redirect('admin/deliveryboys')->with('success', trans('messages.Deliveryboy Update'));
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
        return redirect('admin/deliveryboys')->with('success', trans('messages.Deliveryboy Delete'));
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
        return redirect('admin/deliveryboys')->with('success', trans('messages.Deliveryboy Restore'));
    }

    /*     * ********Filter deliveryboy*********** */

    public function filterdeliveryboys() {
        $search = Input::get('name');
        $branch = Input::get('branch');
        $status = Input::get('status');

        $deliveryboys = DB::table('deliveryboys')
						->join('deliveryboy_description', 'deliveryboys.id', '=', 'deliveryboy_description.deliveryboy_id')
						->join('branch_description', 'deliveryboys.branch_id', '=', 'branch_description.branch_id')
						->SelectRaw(DB::getTablePrefix().'deliveryboys.*,'.DB::getTablePrefix().'deliveryboy_description.deliveryboy_name as name,'.DB::getTablePrefix().'branch_description.branch_name as branch')
						->where(function($query) use($search, $status) {
							if ($search != '') 
              {
								$query->where('deliveryboy_description.deliveryboy_name', 'like', '%' . $search . '%')
								->OrWhere('deliveryboys.mobile', 'like', '%' . $search . '%')
								->OrWhere('deliveryboys.email', 'like', '%' . $search . '%');
							} 
              if ($status == '') 
              {
								$query->where('deliveryboys.is_delete', 0);
							}
						})
						->where(function($qry) use($status, $branch) {
							if ($status != '') 
              {
								if ($status == 'deleted') 
                {
									$qry->where('deliveryboys.is_delete', 1);
								} 
                else 
                {
									$qry->where('deliveryboys.status', $status)->where('deliveryboys.is_delete', 0);
								}
							}
							if($branch != '')
							{
								$qry->where('branch_description.branch_id', $branch);
							}
						})
						->where('deliveryboy_description.language', $this->current_language)
            ->where('branch_description.language', $this->current_language)
						->orderby('deliveryboys.id', 'desc')
						->paginate(10);
		$branches = $this->branch->getbranches();
		return view('admin/deliveryboys', array('deliveryboys' => $deliveryboys, 'branches' => $branches));
    }

    public function trackDeliveryboys()
    {
      $deliveryboys = $this->deliveryboy->getDookDeliveryBoys();

      return view('admin/track_deliveryboys', ['deliveryboys' => $deliveryboys]);
    }
    
   
}
