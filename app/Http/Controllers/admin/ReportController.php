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
use App\User;
use App\Ingredient;
use App\Branch;
use App\Category;
use App\Order;
use App\Deliveryboy;
use URL;
use Cart;
use View;
use Redirect;

class ReportController extends Controller {

	public function __construct(Guard $auth, User $user, Order $order, Ingredient $ingredient, Branch $branch, Category $category, Deliveryboy $deliveryboy)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
		$this->user = $user;
		$this->ingredient = $ingredient;
		$this->order = $order;
		$this->branch = $branch;
		$this->category = $category;
		$this->deliveryboy = $deliveryboy;
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
	
	public function branch_report()
	{
		$from_date = (Input::get('from_date') != '') ? date('Y-m-d', strtotime(Input::get('from_date'))) : '';
		$to_date = (Input::get('to_date') != '') ? date('Y-m-d', strtotime(Input::get('to_date'))) : '';
		$data = DB::table('branches')
				->join('orders', 'branches.id', '=', 'orders.branch_id')
				->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
				->selectRaw('branch_name as branch, sum(order_total) as total_amount, count('.$this->prefix.'orders.id) as order_count')
				->where('payment_status', 's')
				->where('language', $this->current_language)
				->where(function($query) use($from_date, $to_date)
				{
					if($from_date != '')
					{
						$query->whereDate('order_datetime', '>=', $from_date);
					}
					if($to_date != '')
					{
						$query->whereDate('order_datetime', '<=', $to_date);
					}
				})
				->groupBy('orders.branch_id')
				->paginate(10);
		return view('admin/branch_report', array('data' => $data));
	}

	public function item_report()
	{
		$from_date = (Input::get('from_date') != '') ? date('Y-m-d', strtotime(Input::get('from_date'))) : '';
		$to_date = (Input::get('to_date') != '') ? date('Y-m-d', strtotime(Input::get('to_date'))) : '';
		$data = DB::table('vendor_items')
				->join('order_itemdetails', 'vendor_items.id', '=', 'order_itemdetails.item_id')
				->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
				->join('orders', 'order_itemdetails.order_id', '=', 'orders.id')
				->selectRaw('item_name as item, sum(quantity*'.$this->prefix.'order_itemdetails.price) as total_amount')
				->where('payment_status', 's')
				->where('language', $this->current_language)
				->where(function($query) use($from_date, $to_date)
				{
					if($from_date != '')
					{
						$query->whereDate('order_datetime', '>=', $from_date);
					}
					if($to_date != '')
					{
						$query->whereDate('order_datetime', '<=', $to_date);
					}
				})
				->groupBy('order_itemdetails.item_id')
				->paginate(10);
		return view('admin/item_report', array('data' => $data));
	}

	public function sales_report()
	{
		$from_date = (Input::get('from_date') != '') ? date('Y-m-d', strtotime(Input::get('from_date'))) : '';
		$to_date = (Input::get('to_date') != '') ? date('Y-m-d', strtotime(Input::get('to_date'))) : '';
		$data = DB::table('orders')
				->leftjoin('orders as o1', function($join)
				{
					$join->on('orders.id', '=', 'o1.id')->where('o1.created_by', '=', 'a');
				})
				->leftjoin('orders as o2', function($join)
				{
					$join->on('orders.id', '=', 'o2.id')->where('o2.created_by', '=', 'u');
				})
				->leftjoin('orders as o3', function($join)
				{
					$join->on('orders.id', '=', 'o3.id')->where('o3.created_by', '=', 'm');
				})
				->SelectRaw('sum('.$this->prefix.'o1.order_total) as admin_total, count('.$this->prefix.'o1.id) as admin_count, sum('.$this->prefix.'o2.order_total) as web_total, count('.$this->prefix.'o2.id) as web_count, sum('.$this->prefix.'o3.order_total) as mobile_total, count('.$this->prefix.'o3.id) as mobile_count')
				->where('orders.payment_status', 's')
				->where(function($query) use($from_date, $to_date)
				{
					if($from_date != '')
					{
						$query->whereDate('orders.order_datetime', '>=', $from_date);
					}
					if($to_date != '')
					{
						$query->whereDate('orders.order_datetime', '<=', $to_date);
					}
				})
				->first();
		$hour_date = (Input::get('hour_date') != '') ? date('Y-m-d', strtotime(Input::get('hour_date'))) : date('Y-m-d');
		$hour_sales = DB::select("SELECT tmstamp, start_time, total_orders, total_sales FROM
			           (SELECT 
			                   DATE_FORMAT(MIN(`created_at`), '%d/%m/%Y' ) AS tmstamp, 
			                   DATE_FORMAT( MIN(`created_at`), '%H:00' ) AS start_time, 
			                   count(`id`) AS total_orders, 
			                   SUM(`order_total`) AS total_sales 
			           FROM `sh_orders` WHERE payment_status = 's' AND DATE(created_at) = '".$hour_date."'
			               GROUP BY 
			                   year(`created_at`),month(`created_at`),day(`created_at`),( 4 * HOUR( `created_at` ) + FLOOR( MINUTE( `created_at` ) / 60 ))
			           ) AS TBL1 ");
		$admin_hour_sales = DB::select("SELECT tmstamp, start_time, total_orders, total_sales FROM
			           (SELECT 
			                   DATE_FORMAT(MIN(`created_at`), '%d/%m/%Y' ) AS tmstamp, 
			                   DATE_FORMAT( MIN(`created_at`), '%H:00' ) AS start_time, 
			                   count(`id`) AS total_orders, 
			                   SUM(`order_total`) AS total_sales 
			           FROM `sh_orders` WHERE payment_status = 's' AND DATE(created_at) = '".$hour_date."' AND created_by = 'a'
			               GROUP BY 
			                   year(`created_at`),month(`created_at`),day(`created_at`),( 4 * HOUR( `created_at` ) + FLOOR( MINUTE( `created_at` ) / 60 ))
			           ) AS TBL1 ");
		$web_hour_sales = DB::select("SELECT tmstamp, start_time, total_orders, total_sales FROM
			           (SELECT 
			                   DATE_FORMAT(MIN(`created_at`), '%d/%m/%Y' ) AS tmstamp, 
			                   DATE_FORMAT( MIN(`created_at`), '%H:00' ) AS start_time, 
			                   count(`id`) AS total_orders, 
			                   SUM(`order_total`) AS total_sales 
			           FROM `sh_orders` WHERE payment_status = 's' AND DATE(created_at) = '".$hour_date."' AND created_by = 'u'
			               GROUP BY 
			                   year(`created_at`),month(`created_at`),day(`created_at`),( 4 * HOUR( `created_at` ) + FLOOR( MINUTE( `created_at` ) / 60 ))
			           ) AS TBL1 ");
		$mobile_hour_sales = DB::select("SELECT tmstamp, start_time, total_orders, total_sales FROM
			           (SELECT 
			                   DATE_FORMAT(MIN(`created_at`), '%d/%m/%Y' ) AS tmstamp, 
			                   DATE_FORMAT( MIN(`created_at`), '%H:00' ) AS start_time, 
			                   count(`id`) AS total_orders, 
			                   SUM(`order_total`) AS total_sales 
			           FROM `sh_orders` WHERE payment_status = 's' AND DATE(created_at) = '".$hour_date."' AND created_by = 'm'
			               GROUP BY 
			                   year(`created_at`),month(`created_at`),day(`created_at`),( 4 * HOUR( `created_at` ) + FLOOR( MINUTE( `created_at` ) / 60 ))
			           ) AS TBL1 ");
	
		//print_r($branch_hour_sales); exit;
		return view('admin/sales_report', array('data' => $data, 'hour_sales' => $hour_sales, 'admin_hour_sales' => $admin_hour_sales, 'web_hour_sales' => $web_hour_sales, 'mobile_hour_sales' => $mobile_hour_sales));
	}

	public function branch_hour_report()
	{
		$hour_date = (Input::get('hour_date') != '') ? date('Y-m-d', strtotime(Input::get('hour_date'))) : date('Y-m-d');
		$branch_hour_sales = DB::select("SELECT branch, tmstamp, start_time, total_orders, total_sales FROM
			           (SELECT 
			                   DATE_FORMAT(MIN(sh_orders.`created_at`), '%d/%m/%Y' ) AS tmstamp, 
			                   DATE_FORMAT( MIN(sh_orders.`created_at`), '%H:00' ) AS start_time, 
			                   count(sh_orders.`id`) AS total_orders, 
			                   SUM(`order_total`) AS total_sales,
			                   sh_branch_description.branch_name as branch 
			           FROM sh_branches 
			           JOIN `sh_orders` ON sh_branches.id = sh_orders.branch_id
			           JOIN `sh_branch_description` ON sh_branches.id = sh_branch_description.branch_id 
			           WHERE payment_status = 's' AND DATE(sh_orders.created_at) = '".$hour_date."' AND language = '".$this->current_language."'
			           GROUP BY sh_orders.branch_id,
			                   year(sh_orders.`created_at`),month(sh_orders.`created_at`),day(sh_orders.`created_at`),( 4 * HOUR( sh_orders.`created_at` ) + FLOOR( MINUTE( sh_orders.`created_at` ) / 60 ))
			           ) AS TBL1 ");
		//print_r($branch_hour_sales); exit;
		return view('admin/branch_hour_sale', array('data' => $branch_hour_sales));
	}

	public function deliveryboy_hour_report()
	{
		$hour_date = (Input::get('hour_date') != '') ? date('Y-m-d', strtotime(Input::get('hour_date'))) : date('Y-m-d');
		$deliveryboy_hour_sales = DB::select("SELECT deliveryboy, tmstamp, start_time, total_orders, total_sales FROM
			           (SELECT 
			                   DATE_FORMAT(MIN(sh_orders.`created_at`), '%d/%m/%Y' ) AS tmstamp, 
			                   DATE_FORMAT( MIN(sh_orders.`created_at`), '%H:00' ) AS start_time, 
			                   count(sh_orders.`id`) AS total_orders, 
			                   SUM(`order_total`) AS total_sales,
			                   sh_deliveryboy_description.deliveryboy_name as deliveryboy 
			           FROM sh_deliveryboys 
			           JOIN `sh_orders` ON sh_deliveryboys.id = sh_orders.deliveryboy_id
			           JOIN `sh_deliveryboy_description` ON sh_deliveryboys.id = sh_deliveryboy_description.deliveryboy_id 
			           WHERE payment_status = 's' AND DATE(sh_orders.created_at) = '".$hour_date."' AND language = '".$this->current_language."'
			           GROUP BY sh_orders.branch_id,
			                   year(sh_orders.`created_at`),month(sh_orders.`created_at`),day(sh_orders.`created_at`),( 4 * HOUR( sh_orders.`created_at` ) + FLOOR( MINUTE( sh_orders.`created_at` ) / 60 ))
			           ) AS TBL1 ");
		//print_r($branch_hour_sales); exit;
		return view('admin/deliveryboy_hour_sale', array('data' => $deliveryboy_hour_sales));
	}

	public function deliveryboy_sales_report()
	{
		$from_date = (Input::get('from_date') != '') ? date('Y-m-d', strtotime(Input::get('from_date'))) : '';
		$to_date = (Input::get('to_date') != '') ? date('Y-m-d', strtotime(Input::get('to_date'))) : '';
		$data = DB::table('deliveryboy_request')
				->join('orders', 'deliveryboy_request.order_id', '=', 'orders.id')
				->join('deliveryboy_description', 'deliveryboy_request.deliveryboy_id', '=', 'deliveryboy_description.deliveryboy_id')
				->selectRaw('deliveryboy_name as deliveryboy, sum(order_total) as total_amount')
				->where('payment_status', 's')
				->where('language', $this->current_language)
				->where(function($query) use($from_date, $to_date)
				{
					if($from_date != '')
					{
						$query->whereDate('order_datetime', '>=', $from_date);
					}
					if($to_date != '')
					{
						$query->whereDate('order_datetime', '<=', $to_date);
					}
				})
				->groupBy('deliveryboy_request.deliveryboy_id')
				->paginate(10);
		return view('admin/delivery_report', array('data' => $data));
	}

	
}
