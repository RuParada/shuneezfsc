<?php 
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Illuminate\Http\Request;
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
use App\Category;
use App\Vendoritem;
use View;
use Cart;
use Redirect;
use DateTime;
use URL;

class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Language $language)
	{
		//$this->middleware('auth');
		$this->language = $language;
		$settings = DB::table('settings')->get();
	    $languages = $this->language->getlanguages();
		foreach ($settings as $setting) 
		{
			$config_data[$setting->setting_name] = $setting->setting_value;
		}
		
		$this->config_data = $config_data;
		View::share (['config_data'=> $config_data, 'languages' => $languages]);
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	 
	public function changelanguage($language)
	{
		$_SESSION['language'] = ($language != '') ? $language : 'en';
		return Redirect::back();
	}
	
	public function index()
	{  
		$branches = DB::table('branches')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->select('branches.id', 'branch_description.branch_name as branch')
						->where('branches.is_delete', 0)
						->where('branches.status', 1)
						->where('branch_description.language', $this->current_language)
						->get();
						
		return view('index', array('branches' => $branches));
	}

	public function orderAssignToDook()
	{
		$date = date('Y-m-d H:i:s', strtotime('+ '.$this->config_data['dook_assign_time'].' minutes'));
		
		$orders = DB::table('orders')
					->join('branches', 'orders.branch_id', '=', 'branches.id')
					->join('branch_description', 'orders.branch_id', '=', 'branch_description.branch_id')
					->join('user_addressbook', 'orders.address_id', '=', 'user_addressbook.id')
					->select('orders.*', 'street', 'city', 'country', 'pickup_point_id', 'branches.latitude', 'branches.longitude', 'branch_name', 'mobile', 'customer_first_name as first_name', 'customer_last_name as last_name', 'customer_mobile', 'user_addressbook.latitude as delivery_latitude', 'user_addressbook.longitude as delivery_longitude', 'user_addressbook.address as delivery_address')
					->where('orders.delivery_type', 'd')
					->where('order_status', $this->config_data['dook_order_status'])
					->where('orders.dook_id', '')
					->where('accepted_at', '<=', $date)
					->where('language', 'en')
					->get();
 //echo '<pre>'; print_r($orders); exit;
		if ( count($orders) ) {
			foreach ($orders as $order) {
				$items = DB::table('order_itemdetails')
							->join('vendor_item_description', 'order_itemdetails.item_id', '=', 'vendor_item_description.item_id')
							->select('item_name as name', 'quantity as qty')
							->where('order_id', $order->id)
							->get();
				$delivery_time = new DateTime(date('Y-m-d H:i:s', strtotime($order->order_datetime)));
				$start_time = new DateTime(date('Y-m-d H:i:s', strtotime($order->order_datetime. "- ".$this->config_data['start_time']." mins")));
				$end_time = new DateTime(date('Y-m-d H:i:s', strtotime($order->order_datetime. "+ ".$this->config_data['end_time']." mins")));

				$delivery_time = ( $delivery_time < $end_time ) ? $end_time : $delivery_time;
				$branch_address = $order->street.', '.$order->city.', '.$order->country;
			//	print_r($end_time); exit;
				$item_details = '';
				foreach($items as $item)
				{
					$item_details .= $item->name.' x'.$item->qty.' pieces'; 
				}

				$fields = array('order' => array(
											 'deliveryTime' => $delivery_time->getTimestamp() * 1000,
											 'expectedPickUpTime' => array('startTime' => $start_time->getTimestamp() * 1000, 'endTime' => $end_time->getTimestamp() * 1000),
											 'cashOnDelivery' => true,
											 'cashOnDeliveryAmount' => round($order->order_total),
											 ),
								'items' => [array('packingList' => $item_details, 'pickupPointId' => $order->pickup_point_id, 'gpsLocation' => ['lat' => $order->latitude, 'lng' => $order->longitude], 'address' => $branch_address, 'title' => $order->branch_name, 'contactName' => $order->branch_name, 'phone' => $order->mobile)],
								'recipient' => array(
													'firstName' => $order->first_name,
													'lastName' => $order->last_name,
													'mobile' => $order->customer_mobile,
													'deliveryPoint' => $order->delivery_address,
													'gpsLocation' => array('lat' => $order->delivery_latitude, 'lng' => $order->delivery_longitude))
												
				);

				$fields = json_encode($fields);
			
				$url = ( $this->config_data['is_dook_production'] == 1 ) ? $this->config_data['dook_production_url'] : $this->config_data['dook_test_url'];
		        $ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url."/api/FleetOwners/createOrder?access_token=".$this->config_data['dook_access_token']."");
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

				$response = curl_exec($ch);
				curl_close($ch);

				$result = json_decode($response);
//print_r($result); exit;
				if ( isset($result->result->order->id) ) {
					$dookId = $result->result->order->id;
					DB::table('orders')->where('id', $order->id)->update(['dook_id' => $dookId]);

					$calback = URL::to('/dook-order-status');
					$postfields = array(
									    "cbUrl" => $calback,
									    "cbHeaders" => array(
									        "User-Agent" => "header_1",
									        "X-Api-Token" => $this->config_data['dook_access_token'],
									        "Content-Type" => "application/json"
									    	)
									  );
						$url =  $url."/api/FleetOwners/".$this->config_data['dook_fleet_owner_id']."?access_token=".$this->config_data['dook_access_token']."";
						$headers = array('Content-Type: application/json');
						$curl = curl_init();
						$postfields = json_encode($postfields);
						curl_setopt($curl, CURLOPT_URL, $url);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
						curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
						curl_setopt($curl, CURLOPT_POST, TRUE);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
						
						$rresponse = curl_exec($curl);
						curl_close($curl);
						$result = json_decode($rresponse);
					//	print_r($result); exit;
				}	
			}
		}			
	}

	public function updateDookOrderStatus()
	{
		$response = file_get_contents("php://input"); 
		$result = json_decode($response, true); 
		$dookResponse = json_encode($response);
		$status = $result['payload']['action'];
		$order_id = $result['payload']['id'];
		
		$order_status = '';
		if ( $status === 'delivered' ) {
			$order_status = 'd';
		}
		elseif ( $status === 'onWayToDelivery' ) {
			$order_status = 'o';
		}
		elseif ( $status === 'waitingForPickup' ) {
			$order_status = 'da';
		}
		elseif ( $status === 'waitingforReturn' ) {
			$order_status = 'r';
		}
		elseif ( $status === 'canceled' ) {
			$order_status = 'ca';
		}
		elseif ( $status === 'pickedup' ) {
			$order_status = 'pi';
		}
		

		if ( $order_status == '' ) {
			DB::table('orders')->where('dook_id', $order_id)->update(['dook_response' => $dookResponse]);
			return 0;
		}
		
		DB::table('orders')->where('dook_id', $order_id)->update(['order_status' => $order_status, 'dook_response' => $dookResponse]);
		return 1;
	}

	public function updateFoodicsOrderStatus()
	{
		$type = Input::get('type');
		$status = Input::get('status');
		$hid = Input::get('hid');

		DB::table('orders')->where('foodics_id', $hid)->update(['dook_response' => $type . ' - ' .$hid . ' - ' .$status]);

		return 1;
	}
}
