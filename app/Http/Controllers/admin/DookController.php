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
use App\Language;
use App\Branch;
use View;
use Redirect;

class DookController extends Controller {

	public function __construct(Guard $auth, Language $language, Branch $branch)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->language = $language;
	    $this->branch = $branch;
	    $this->prefix = DB::getTablePrefix();
	    $this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	    $settings = DB::table('settings')->get();
	    foreach ($settings as $setting) 
		{
			$config_data[$setting->setting_name] = $setting->setting_value;
		}
		
		$this->config_data = $config_data;
		
	}

	public function getTeams()
	{
		// $fields = ['email' => 'shuneez@dook.sa',
  //       				'password' => '1234567'
  //       				];
  //       	$fields = json_encode($fields);

  //       	$header = array();
		// 	$header[] = 'Content-type: application/json; charset=utf-8';
			
		// 	$ch = curl_init();
		// 	curl_setopt($ch, CURLOPT_URL, "https://test-api.dook.sa/api/Auths/login");
		// 	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		// 	curl_setopt($ch, CURLOPT_HEADER, FALSE);
		// 	curl_setopt($ch, CURLOPT_POST, TRUE);
		// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		// 	$response = curl_exec($ch);
		// 	curl_close($ch);
		// 	$data = json_decode($response);
		// 	echo '<pre>'; print_r($data); exit;
		
		$name = Input::get('name');
		$teams = DB::table('dook_teams')
				->join('branch_description', 'dook_teams.branch_id', '=', 'branch_description.branch_id')
				->select('dook_teams.*', 'branch_name as branch')
				->where(function($query) use($name)
					{
						if ( $name != '' )
							$query->where('name', 'like', '%'.$name.'%');
					})
				->where('language', 'en')
				->latest('dook_teams.id')
				->paginate(10);

		return view('admin/dook_teams', ['teams' => $teams]);
	}

	public function createTeamForm()
	{
		$countries = getDookCounties();

		$branches = $this->branch->getbranches();
		
		return view('admin/addteam', ['countries' => $countries, 'branches' => $branches]);
	}
	
	public function createTeam()
	{
		$valid = Validator::make(Input::all(), 
					['name' => 'required|unique:dook_teams,name',
					'country' => 'required',
                    'city' => 'required', 
                    'branch' => 'required'
                    ]);

        if ($valid->fails()) {
            return Redirect::back()->withInput(Input::all())->with('error', $valid->errors());
        } 
        else {

        	$fields = ['name' => Input::get('name'),
        				'autoAssign' => Input::get('auto_assign'),
        				'cityId' => Input::get('city')];
        	$fields = json_encode($fields);

        	$header = array();
			$header[] = 'Content-type: application/json; charset=utf-8';
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://test-api.dook.sa/api/Companies/".$this->config_data['dook_company_id']."/teams?access_token=".$this->config_data['dook_access_token']);
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
			DB::table('dook_teams')->insert(['branch_id' => Input::get('branch'), 'name' => Input::get('name'), 'auto_assign' => Input::get('auto_assign'), 'city_id' => Input::get('city'), 'country_id' => Input::get('country'), 'city' => Input::get('city_name'), 'country' => Input::get('country_name'), 'dook_id' => $data->id]);

			return redirect('admin/teams')->with('success', trans('messages.New team created successfully'));
			
		}
	}

	public function getDookCity()
	{
		$country_id = Input::get('country_id');

		$citylist = getDookCities($country_id);
		
		$cityresult = "<option value=''>".trans('messages.Select City')."</option>";
		if(count($citylist))
		{
			foreach ($citylist as $city) 
			{
				$cityresult .= "<option value='".$city->id."'>".ucfirst($city->name->en)."</option>";
			}
		}
		$result = array('citylist' => $cityresult);
		return json_encode($result);

	}

	public function getPickupPoints()
	{
		$team_id = Input::get('team');
		$name = Input::get('name'); 
		$teams = DB::table('dook_teams')->latest()->get();

		$pickup_points = DB::table('pickup_points')
						->join('dook_teams', 'pickup_points.team_dook_id', '=', 'dook_teams.dook_id')
						->select('pickup_points.*', 'name')
						->where(function($query) use($team_id)
						{
							if ( $team_id != '' )
								$query->where('team_dook_id', $team_id);
						})
						->where(function($query) use($name)
						{
							if ( $name != '' )
								$query->where('address', 'like', '%'.$name.'%')->OrWhere('title', 'like', '%'.$name.'%');
						})
						->latest()
						->paginate(10);

		return view('admin/pickup_points', ['pickup_points' => $pickup_points, 'teams' => $teams]);
	}

	public function createPickupPointForm()
	{
		$existTeam = [];
		$pickupPoints = DB::table('pickup_points')->get();
		if ( count($pickupPoints) ) {
			foreach ($pickupPoints as $pickupPoint) {
				$existTeam[] = $pickupPoint->team_dook_id;
			}
		}
		$teams = DB::table('dook_teams')->whereNotIn('dook_id', $existTeam)->latest()->get();

		return view('admin/addpickup_point', ['teams' => $teams]);
	}

	public function createPickupPoint()
	{
		$valid = Validator::make(Input::all(), 
					['address' => 'required',
					'latitude' => 'required',
                    'longitude' => 'required',
                    'team_id' => 'required',
                    'contact_name' => 'required',
                    'phone' => 'required|digits_between:9,13|numeric',
                    'title' => 'required'
                    ]);

        if ($valid->fails()) {
        	return Redirect::back()->withInput(Input::all())->with('error', $valid->errors());
        } 
        else {
        	$fields = ['address' => Input::get('address'),
        			   'gpsLocation' => ['lat' => Input::get('latitude'), 'lng' => Input::get('longitude')],
        			   'title' => Input::get('title'),
        			   'contactName' => Input::get('contact_name'),
        			   'phone' => Input::get('phone'),
        			   'teamId' => Input::get('team_id')];
        	$fields = json_encode($fields);

        	$header = array();
			$header[] = 'Content-type: application/json; charset=utf-8';
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://test-api.dook.sa/api/PickUpPoints?access_token=".$this->config_data['dook_access_token']);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			$data = json_decode($response);

			DB::table('pickup_points')->insert(['address' => Input::get('address'), 'latitude' => Input::get('latitude'), 'longitude' => Input::get('longitude'), 'title' => Input::get('title'), 'contact_name' => Input::get('contact_name'), 'phone' => Input::get('phone'), 'team_dook_id' => Input::get('team_id'), 'dook_id' => $data->id, 'created_at' => date('Y-m-d H:i:s')]);

			$branch = DB::table('dook_teams')->where('dook_id', Input::get('team_id'))->first();

			DB::table('branches')->where('id', $branch->branch_id)->update(['pickup_point_id' => $data->id]); 
			
			return redirect('admin/pickup-points')->with('success', trans('messages.Pickup point added successfully'));
        }
	}

	public function deletePickupPoint($id)
	{
		$header = array();
		$header[] = 'Content-type: application/json; charset=utf-8';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://test-api.dook.sa/api/PickUpPoints/".$id."?access_token=".$this->config_data['dook_access_token']);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

		DB::table('pickup_points')->where('dook_id', $id)->delete();

		return Redirect::back()->with('success', trans('messages.Pickup point deleted successfully'));
	}

	public function getBranchTeam()
	{
		$branch_id = Input::get('branch_id');

		$team = DB::table('dook_teams')->where('branch_id', $branch_id)->first();
		if ( count($team) ) {
			$teamName = $team->name;
			$teamId = $team->dook_id;
		}
		else {
			$teamName = '';
			$teamId = '';
		}

		$result = array('team' => $teamName, 'teamId' => $teamId);
		return json_encode($result);
	}
	
}
