<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/welcome', function () {
    return view('welcome');
});


/*Route::filter('no-cache',function($route, $request, $response){

$response->headers->set('Cache-Control','nocache, no-store, max-age=0, must-revalidate');
$response->headers->set('Pragma','no-cache');
$response->headers->set('Expires','Fri, 01 Jan 1990 00:00:00 GMT');

});*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*********** Admin Auth Controller ****************/

Route::get('/admin', 'admin\AdminauthenticateController@login');
Route::post('/admin/login', 'admin\AdminauthenticateController@postlogin');
Route::get('/admin/forgotpassword', 'admin\AdminauthenticateController@forgotpassword');

Route::get('/branch-login', 'branch\BranchauthenticateController@login');
Route::post('/branch/login', 'branch\BranchauthenticateController@postlogin');
Route::get('/branch/forgotpassword', 'branch\BranchauthenticateController@forgotpassword');

Route::get('/staff-login', 'branch\BranchauthenticateController@stafflogin_form');
Route::post('/branch/stafflogin', 'branch\BranchauthenticateController@stafflogin');
Route::get('/branch/staff_forgotpassword', 'branch\BranchauthenticateController@staff_forgotpassword');

Route::group(array('before' => 'auth', 'after' => 'no-cache'), function()
{
	/************ Ingredients Controller *******************/
	
	Route::get('/admin/ingredients', 'admin\IngredientController@getingredients');
	Route::get('/admin/addingredient', 'admin\IngredientController@addingredient_form');
	Route::post('/admin/addingredient', 'admin\IngredientController@addingredient');
	Route::get('/admin/editingredient/{id?}', 'admin\IngredientController@getingredient');
	Route::post('/admin/updateingredient', 'admin\IngredientController@updateingredient');
	Route::get('/admin/change_ingredientstatus', 'admin\IngredientController@change_ingredientstatus');
	Route::get('/admin/filteringredient', 'admin\IngredientController@filteringredient');
	Route::get('/admin/deleteingredient/{id?}', 'admin\IngredientController@deleteingredient');
	Route::get('/admin/restoreingredient/{id?}', 'admin\IngredientController@restoreingredient');
	Route::get('/admin/removeingredientlist', 'admin\IngredientController@removeingredientlist');
	Route::get('/admin/ingredientlist', 'admin\IngredientController@ingredientlist');
	Route::get('/admin/deleteingredientlist/{id?}', 'admin\IngredientController@deleteingredientlist');
	Route::get('/admin/saveingredientlist', 'admin\IngredientController@saveingredientlist');

	/******************** Execlusion Controller *********************/

	Route::get('/admin/execlusions', 'admin\ExeclusionController@getexeclusions');
	Route::get('/admin/addexeclusion', 'admin\ExeclusionController@addexeclusion_form');
	Route::post('/admin/addexeclusion', 'admin\ExeclusionController@addexeclusion');
	Route::get('/admin/editexeclusion/{id?}', 'admin\ExeclusionController@getexeclusion');
	Route::post('/admin/updateexeclusion', 'admin\ExeclusionController@updateexeclusion');
	Route::get('/admin/change_execlusionstatus', 'admin\ExeclusionController@change_execlusionstatus');
	Route::get('/admin/filterexeclusion', 'admin\ExeclusionController@filterexeclusion');
	Route::get('/admin/deleteexeclusion/{id?}', 'admin\ExeclusionController@deleteexeclusion');
	Route::get('/admin/restoreexeclusion/{id?}', 'admin\ExeclusionController@restoreexeclusion');

	/************ Admin Controller*********************/
	
	Route::get('/admin/dashboard', 'admin\AdminController@dashboard');
	Route::get('/admin/filter_dashboard/{branch_id?}', 'admin\AdminController@dashboard');
	Route::get('/admin/logout', 'admin\AdminController@logout');
	Route::get('/admin/changelanguage/{language?}', 'admin\AdminController@changelanguage');
	Route::get('/admin/settings', 'admin\AdminController@settings');
	Route::post('/admin/updateadmin', 'admin\AdminController@updateadmin');
	Route::post('/admin/updateconfig_settings', 'admin\AdminController@updateconfig_settings');
	Route::post('/admin/updatesite_settings', 'admin\AdminController@updatesite_settings');
    Route::post('/admin/updatesmtp_settings', 'admin\AdminController@updatesmtp_settings');
    Route::post('/admin/updateimage_settings', 'admin\AdminController@updateimage_settings');
	
	/************ Category Controller *******************/
	
	Route::get('/admin/categories', 'admin\CategoryController@getcategories');
	Route::get('/admin/addcategory', 'admin\CategoryController@addcategory_form');
	Route::post('/admin/addcategory', 'admin\CategoryController@addcategory');
	Route::get('/admin/editcategory/{id?}', 'admin\CategoryController@getcategory');
	Route::post('/admin/updatecategory', 'admin\CategoryController@updatecategory');
	Route::get('/admin/change_categorystatus', 'admin\CategoryController@change_categorystatus');
	Route::get('/admin/filtercategories', 'admin\CategoryController@filtercategories');
	Route::get('/admin/deletecategory/{id?}', 'admin\CategoryController@deletecategory');
	Route::get('/admin/restorecategory/{id?}', 'admin\CategoryController@restorecategory');
	
	/************* Subcategory Controller *********************/
	
	Route::get('/admin/subcategories', 'admin\SubcategoryController@getsubcategories');
	Route::get('/admin/addsubcategory', 'admin\SubcategoryController@addsubcategory_form');
	Route::post('/admin/addsubcategory', 'admin\SubcategoryController@addsubcategory');
	Route::get('/admin/editsubcategory/{id?}', 'admin\SubcategoryController@getsubcategory');
	Route::post('/admin/updatesubcategory', 'admin\SubcategoryController@updatesubcategory');
	Route::get('/admin/change_subcategorystatus', 'admin\SubcategoryController@change_subcategorystatus');
	Route::get('/admin/filtersubcategories', 'admin\SubcategoryController@filtersubcategories');
	Route::get('/admin/deletesubcategory/{id?}', 'admin\SubcategoryController@deletesubcategory');
	Route::get('/admin/restoresubcategory/{id?}', 'admin\SubcategoryController@restoresubcategory');
	
	/*************** Cuisine Controller **************************/
	
	Route::get('/admin/cuisines', 'admin\CuisineController@getcuisines');
	Route::get('/admin/addcuisine', 'admin\CuisineController@addcuisine_form');
	Route::post('/admin/addcuisine', 'admin\CuisineController@addcuisine');
	Route::get('/admin/editcuisine/{id?}', 'admin\CuisineController@getcuisine');
	Route::post('/admin/updatecuisine', 'admin\CuisineController@updatecuisine');
	Route::get('/admin/change_cuisinestatus', 'admin\CuisineController@change_cuisinestatus');
	Route::get('/admin/filtercuisines', 'admin\CuisineController@filtercuisines');
	Route::get('/admin/deletecuisine/{id?}', 'admin\CuisineController@deletecuisine');
	
	/************* Vendor Controller *********************/
	
	Route::get('/admin/vendors', 'admin\VendorController@getvendors');
	Route::get('/admin/addvendor', 'admin\VendorController@addvendor_form');
	Route::post('/admin/addvendor', 'admin\VendorController@addvendor');
	Route::get('/admin/editvendor/{id?}', 'admin\VendorController@getvendor');
	Route::post('/admin/updatevendor', 'admin\VendorController@updatevendor');
	Route::get('/admin/change_vendorstatus', 'admin\VendorController@change_vendorstatus');
	Route::get('/admin/filtervendors', 'admin\VendorController@filtervendors');
	Route::get('/admin/deletevendor/{id?}', 'admin\VendorController@deletevendor');
	Route::get('/admin/restorevendor/{id?}', 'admin\VendorController@restorevendor');
	
	/************* Branch Controller *********************/
	
	Route::get('/admin/branches', 'admin\BranchController@getbranches');
	Route::get('/admin/addbranch', 'admin\BranchController@addbranch_form');
	Route::post('/admin/addbranch', 'admin\BranchController@addbranch');
	Route::get('/admin/editbranch/{id?}', 'admin\BranchController@getbranch');
	Route::post('/admin/updatebranch', 'admin\BranchController@updatebranch');
	Route::get('/admin/change_branchstatus', 'admin\BranchController@change_branchstatus');
	Route::get('/admin/filterbranches', 'admin\BranchController@filterbranches');
	Route::get('/admin/deletebranch/{id?}', 'admin\BranchController@deletebranch');
	Route::get('/admin/restorebranch/{id?}', 'admin\BranchController@restorebranch');
	Route::get('/admin/delivery_area', 'admin\BranchController@delivery_area');
	Route::get('/admin/branch_workingtime/{id?}', 'admin\BranchController@branch_workingtime');
	Route::get('/admin/delete_timeslot/{id?}', 'admin\BranchController@delete_timeslot');
	Route::post('/admin/update_branch_workingtime', 'admin\BranchController@update_branch_workingtime');
	
	/************* CMS Controller *********************/
	
	Route::get('/admin/cms', 'admin\CmsController@getpages');
	Route::get('/admin/addcms', 'admin\CmsController@addpage_form');
	Route::post('/admin/addcms', 'admin\CmsController@addpage');
	Route::get('/admin/getcms/{id?}', 'admin\CmsController@getpage');
	Route::post('/admin/updatecms', 'admin\CmsController@updatepage');
	Route::get('/admin/change_pagestatus', 'admin\CmsController@change_pagestatus');
	Route::get('/admin/deletepage/{id?}', 'admin\CmsController@deletepage');
	Route::get('/admin/filtercms', 'admin\CmsController@filterpages');
	
	/************* User Controller *********************/
	
	Route::get('/admin/users', 'admin\UserController@getusers');
	Route::get('/admin/adduser', 'admin\UserController@adduser_form');
	Route::post('/admin/adduser', 'admin\UserController@adduser');
	Route::get('/admin/change_userstatus', 'admin\UserController@change_userstatus');
	Route::get('/admin/getuser/{id?}', 'admin\UserController@getuser');
	Route::post('/admin/updateuser', 'admin\UserController@updateuser');
	Route::get('/admin/deleteuser/{id?}', 'admin\UserController@deleteuser');
	Route::get('/admin/restoreuser/{id?}', 'admin\UserController@restoreuser');
	Route::get('/admin/filterusers', 'admin\UserController@filterusers');
	Route::get('/admin/sendnewsletter', 'admin\UserController@sendnewsletter_form');
	Route::post('/admin/sendnewsletter', 'admin\UserController@sendnewsletter');
	Route::get('/admin/subscribers', 'admin\UserController@subscribers');
	Route::get('/admin/filtersubscribers', 'admin\UserController@filtersubscribers');
	Route::get('/admin/deletesubscriber/{id?}', 'admin\UserController@deletesubscriber');
	
	/************* Staff Controller *********************/
	
	Route::get('/admin/staffs', 'admin\StaffController@getstaffs');
	Route::get('/admin/addstaff', 'admin\StaffController@addstaff_form');
	Route::post('/admin/addstaff', 'admin\StaffController@addstaff');
	Route::get('/admin/change_staffstatus', 'admin\StaffController@change_staffstatus');
	Route::get('/admin/getstaff/{id?}', 'admin\StaffController@getstaff');
	Route::post('/admin/updatestaff', 'admin\StaffController@updatestaff');
	Route::get('/admin/deletestaff/{id?}', 'admin\StaffController@deletestaff');
	Route::get('/admin/filterstaffs', 'admin\StaffController@filterstaffs');
	
	/************* Admin User Controller *********************/
	
	Route::get('/admin/adminusers', 'admin\AdminuserController@getadminusers');
	Route::get('/admin/addadminuser', 'admin\AdminuserController@addadminuser_form');
	Route::post('/admin/addadminuser', 'admin\AdminuserController@addadminuser');
	Route::get('/admin/change_adminuserstatus', 'admin\AdminuserController@change_adminuserstatus');
	Route::get('/admin/getadminuser/{id?}', 'admin\AdminuserController@getadminuser');
	Route::post('/admin/updateadminuser', 'admin\AdminuserController@updateadminuser');
	Route::get('/admin/deleteadminuser/{id?}', 'admin\AdminuserController@deleteadminuser');
	Route::get('/admin/filteradminusers', 'admin\AdminuserController@filteradminusers');
	
	/************* FAQ Controller **********************/
	
	Route::get('/admin/faqs', 'admin\FaqController@getfaqs');
	Route::get('/admin/addfaq', 'admin\FaqController@addfaq_form');
    Route::post('/admin/addfaq', 'admin\FaqController@addfaq');
	Route::get('/admin/getfaq/{id?}', 'admin\FaqController@getfaq');
	Route::post('/admin/updatefaq', 'admin\FaqController@updatefaq');
	Route::get('/admin/change_faqstatus', 'admin\FaqController@change_faqstatus');
	Route::get('/admin/deletefaq/{id?}', 'admin\FaqController@deletefaq');
	Route::get('/admin/filterfaqs/{status?}', 'admin\FaqController@filterfaqs');
	
	/******************* Enquiry Controller *******************/
	
	Route::get('/admin/enquires', 'admin\EnquiryController@getenquires');
	Route::get('/admin/viewenquiry/{id?}', 'admin\EnquiryController@viewenquiry');
	Route::get('/admin/delete_enquiry/{id?}', 'admin\EnquiryController@delete_enquiry');
	Route::get('/admin/filterenquires/{status?}', 'admin\EnquiryController@filterenquires');
	
	/**************** Promocode Controller *******************/
	
	Route::get('/admin/promocodes', 'admin\PromocodeController@getpromocodes');
	Route::get('/admin/addpromocode', 'admin\PromocodeController@addpromocode_form');
	Route::post('/admin/addpromocode', 'admin\PromocodeController@addpromocode');
	Route::get('/admin/editpromocode/{id?}', 'admin\PromocodeController@getpromocode');
	Route::post('/admin/updatepromocode', 'admin\PromocodeController@updatepromocode');
	Route::get('/admin/change_promocodestatus', 'admin\PromocodeController@change_promocodestatus');
	Route::get('/admin/filterpromocodes', 'admin\PromocodeController@filterpromocodes');
	Route::get('/admin/deletepromocode/{id?}', 'admin\PromocodeController@deletepromocode');
	Route::get('/admin/sendpromocode/{id?}', 'admin\PromocodeController@sendpromocode_form');
	Route::post('/admin/mailpromocode', 'admin\PromocodeController@sendpromocode');
	
	/************ Deliveryboy Controller *******************/
	
	Route::get('/admin/deliveryboys', 'admin\DeliveryboyController@getdeliveryboys');
	Route::get('/admin/adddeliveryboy', 'admin\DeliveryboyController@adddeliveryboy_form');
	Route::post('/admin/adddeliveryboy', 'admin\DeliveryboyController@adddeliveryboy');
	Route::get('/admin/editdeliveryboy/{id?}', 'admin\DeliveryboyController@getdeliveryboy');
	Route::post('/admin/updatedeliveryboy', 'admin\DeliveryboyController@updatedeliveryboy');
	Route::get('/admin/change_deliveryboystatus', 'admin\DeliveryboyController@change_deliveryboystatus');
	Route::get('/admin/filterdeliveryboy', 'admin\DeliveryboyController@filterdeliveryboys');
	Route::get('/admin/deletedeliveryboy/{id?}', 'admin\DeliveryboyController@deletedeliveryboy');
	Route::get('/admin/restoredeliveryboy/{id?}', 'admin\DeliveryboyController@restoredeliveryboy');
	Route::get('/admin/track-deliveryboys', 'admin\DeliveryboyController@trackDeliveryboys');
	
	/************ Currency Controller *******************/
	
	Route::get('/admin/currencies', 'admin\CurrencyController@getcurrencies');
	Route::get('/admin/addcurrency', 'admin\CurrencyController@addcurrency_form');
	Route::post('/admin/addcurrency', 'admin\CurrencyController@addcurrency');
	Route::get('/admin/editcurrency/{id?}', 'admin\CurrencyController@getcurrency');
	Route::post('/admin/updatecurrency', 'admin\CurrencyController@updatecurrency');
	Route::get('/admin/change_currencystatus', 'admin\CurrencyController@change_currencystatus');
	Route::get('/admin/deletecurrency/{id?}', 'admin\CurrencyController@deletecurrency');
	Route::get('/admin/restorecurrency/{id?}', 'admin\CurrencyController@restorecurrency');
	Route::get('/admin/filtercurrencies/{status?}', 'admin\CurrencyController@filtercurrencies');

	/************ Address Type Controller *******************/
	
	Route::get('/admin/addresstype', 'admin\AddresstypeController@getaddresstype');
	Route::get('/admin/add_addresstype', 'admin\AddresstypeController@add_addresstype_form');
	Route::post('/admin/add_addresstype', 'admin\AddresstypeController@add_addresstype');
	Route::get('/admin/edit_addresstype/{id?}', 'admin\AddresstypeController@getaddress');
	Route::post('/admin/update_addresstype', 'admin\AddresstypeController@update_addresstype');
	Route::get('/admin/change_addresstypestatus', 'admin\AddresstypeController@change_addresstypestatus');
	Route::get('/admin/delete_addresstype/{id?}', 'admin\AddresstypeController@delete_addresstype');
	Route::get('/admin/restore_addresstype/{id?}', 'admin\AddresstypeController@restore_addresstype');
	Route::get('/admin/filter_addresstype/{status?}', 'admin\AddresstypeController@filter_addresstype');
	
	/************* Vendor Item Controller *********************/
	
	Route::get('/admin/vendoritems', 'admin\VendoritemController@getvendoritems');
	Route::get('/admin/addvendor_item', 'admin\VendoritemController@addvendoritem_form');
	Route::post('/admin/addvendoritem', 'admin\VendoritemController@addvendoritem');
	Route::get('/admin/editvendor_item/{id?}', 'admin\VendoritemController@getvendoritem');
	Route::post('/admin/updatevendoritem', 'admin\VendoritemController@updatevendoritem');
	Route::get('/admin/change_vendoritemstatus', 'admin\VendoritemController@change_vendoritemstatus');
	Route::get('/admin/filtervendor_items', 'admin\VendoritemController@filtervendoritems');
	Route::get('/admin/deletevendoritem/{id?}', 'admin\VendoritemController@deletevendoritem');
	Route::get('/admin/restorevendoritem/{id?}', 'admin\VendoritemController@restorevendoritem');
	Route::get('/admin/getvendorcategories', 'admin\VendoritemController@getvendorcategories');
	Route::get('/admin/getvendorsubcategories', 'admin\VendoritemController@getvendorsubcategories');
	Route::get('/admin/getingredientlist', 'admin\VendoritemController@getingredientlist');
	Route::get('/admin/savesize', 'admin\VendoritemController@savesize');
	Route::get('/admin/removesize', 'admin\VendoritemController@removesize');
	Route::get('/admin/viewitem', 'admin\VendoritemController@viewItem');
	Route::get('/admin/viewingredient', 'admin\VendoritemController@viewIngredient');
	
	/**************************** Foodics **************************/

	Route::get('/admin/item_integration', 'admin\FoodicsController@getFoodicsItems');
	Route::get('/admin/updatefoodics_item', 'admin\FoodicsController@updateFoodicsItem');
	Route::get('/admin/removefoodicsitem/{id?}', 'admin\FoodicsController@removeFoodicsItem');
	
	Route::get('/admin/ingredient_integration', 'admin\FoodicsController@getFoodicsIngreidents');
	Route::get('/admin/updatefoodics_ingredient', 'admin\FoodicsController@updateFoodicsIngreident');
	Route::get('/admin/removefoodicsingredient/{id?}', 'admin\FoodicsController@removeFoodicsIngredient');
	
	Route::get('/admin/ingredientlist_integration', 'admin\FoodicsController@getFoodicsIngredientList');
	Route::get('/admin/updatefoodics_ingredientlist', 'admin\FoodicsController@updateFoodicsIngredientList');
	Route::get('/admin/removefoodicsingredientlist/{id?}', 'admin\FoodicsController@removeFoodicsIngredientList');
	
	Route::get('/admin/addresstype_integration', 'admin\FoodicsController@getFoodicsAddressType');
	Route::get('/admin/updatefoodics_addresstype', 'admin\FoodicsController@updateFoodicsAddressType');
	Route::get('/admin/removefoodicsaddresstype/{id?}', 'admin\FoodicsController@removeFoodicsAddressType');
	
	Route::get('/admin/execlusion_integration', 'admin\FoodicsController@getFoodicsExeclusions');
	Route::get('/admin/updatefoodics_execlusion', 'admin\FoodicsController@updateFoodicsExeclusion');
	Route::get('/admin/removefoodicsexeclusion/{id?}', 'admin\FoodicsController@removeFoodicsExeclusion');
	
	Route::get('/admin/branch-integration', 'admin\FoodicsController@getFoodicsBranch');
	Route::get('/admin/update-foodics-branch', 'admin\FoodicsController@updateFoodicsBranch');
	Route::get('/admin/remove-foodics-branch/{id?}', 'admin\FoodicsController@removeFoodicsBranch');
	
	/******************* Order Controller ********************/
	
	Route::get('/admin/orders', 'admin\OrderController@getorders');
	Route::get('/admin/createorder', 'admin\OrderController@createorder_form');
	Route::get('/admin/getcustomer', 'admin\OrderController@getcustomer');
	Route::post('/admin/createorder', 'admin\OrderController@createorder');
	Route::get('/admin/getbranch_deliveryboys', 'admin\OrderController@getbranch_deliveryboys');
	Route::get('/admin/getbranch_delivery', 'admin\OrderController@getbranch_delivery');
	Route::get('/admin/selectingredient/{id?}', 'admin\OrderController@selectingredient');
	Route::get('/admin/getcategory_items', 'admin\OrderController@getcategory_items');
	Route::get('/admin/addtocart', 'admin\OrderController@addtocart');
	Route::get('/admin/add_remove_quantity', 'admin\OrderController@add_remove_quantity');
	Route::get('/admin/delete_cartitem', 'admin\OrderController@delete_cartitem');
	Route::get('/admin/additem', 'admin\OrderController@additem');
	Route::get('/admin/editorder/{id}', 'admin\OrderController@editorder');
	Route::post('/admin/updateorder', 'admin\OrderController@updateorder');
	Route::get('/admin/updateqty', 'admin\OrderController@updateqty');
	Route::get('/admin/delete_item', 'admin\OrderController@delete_item');
	Route::post('/admin/getorder_branches', 'admin\OrderController@getorder_branches');
	Route::post('/admin/update_orderstatus', 'admin\OrderController@update_orderstatus');
	Route::get('/admin/assign_order/{id?}', 'admin\OrderController@assign_order');
	Route::get('/admin/assign_deliveryboy/{id?}/{order_id?}', 'admin\OrderController@assign_deliveryboy');
	Route::get('/admin/deleteorder/{id?}', 'admin\OrderController@deleteorder');
	Route::get('/admin/deliveryboy-ratings', 'admin\OrderController@deliveryboyRatings');
	Route::get('/admin/filter-ratings', 'admin\OrderController@deliveryboyRatings');
	
	/******************* City Controller ********************/
	
	Route::get('/admin/citylist', 'admin\CityController@getcities');
	Route::post('/admin/addcity', 'admin\CityController@addcity');
	Route::get('/admin/editcity/{id?}', 'admin\CityController@getcity');
	Route::post('/admin/updatecity', 'admin\CityController@updatecity');
	Route::get('/admin/change_citystatus', 'admin\CityController@change_citystatus');
	Route::get('/admin/deletecity/{id?}', 'admin\CityController@deletecity');
	Route::get('/admin/filtercity/{status?}', 'admin\CityController@filtercity');
	
	Route::get('/admin/backend_languages', 'admin\LanguageController@getbackend_languages');
	Route::post('/admin/update_backend_languages', 'admin\LanguageController@update_backend_languages');
	Route::get('/admin/api_languages', 'admin\LanguageController@getapi_languages');
	Route::post('/admin/update_api_languages', 'admin\LanguageController@update_api_languages');
	Route::get('/admin/frontend_languages', 'admin\LanguageController@getfrontend_languages');
	Route::post('/admin/update_frontend_languages', 'admin\LanguageController@update_frontend_languages');

	/**********************Report Controller*******************/

	Route::get('/admin/branch_report', 'admin\ReportController@branch_report');
	Route::get('/admin/item_report', 'admin\ReportController@item_report');
	Route::get('/admin/sales_report', 'admin\ReportController@sales_report');
	Route::get('/admin/branch_hour_report', 'admin\ReportController@branch_hour_report');
	Route::get('/admin/deliveryboy_hour_report', 'admin\ReportController@deliveryboy_hour_report');
	Route::get('/admin/deliveryboy_sales_report', 'admin\ReportController@deliveryboy_sales_report');

	/******************** Foodics **************************/

	Route::get('/admin/foodics', 'admin\VendoritemController@getFoodicsItems');

	/******************** Dook **************************/

	Route::get('/admin/teams', 'admin\DookController@getTeams');
	Route::get('/admin/create-team', 'admin\DookController@createTeamForm');
	Route::post('/admin/create-team', 'admin\DookController@createTeam');
	Route::get('/admin/filter-teams', 'admin\DookController@getTeams');
	Route::get('/admin/get-dook-city', 'admin\DookController@getDookCity');
	Route::get('/admin/get-branch-team', 'admin\DookController@getBranchTeam');

	Route::get('/admin/pickup-points', 'admin\DookController@getPickupPoints');
	Route::get('/admin/create-pickup-point', 'admin\DookController@createPickupPointForm');
	Route::post('/admin/create-pickup-point', 'admin\DookController@createPickupPoint');
	Route::get('/admin/filter-pickup-points', 'admin\DookController@getPickupPoints');
	Route::get('/admin/delete-pickup-point/{id?}', 'admin\DookController@deletePickupPoint');
	
	
	/*********** Branch Login Controller *****************/
	
	Route::get('/branch/dashboard', 'branch\BranchController@dashboard');
	Route::get('/branch/changelanguage/{language?}', 'branch\BranchController@changelanguage');
	Route::get('/branch/editbranch', 'branch\BranchController@getbranch');
	Route::post('/branch/updatebranch', 'branch\BranchController@updatebranch');
	Route::get('/branch/logout', 'branch\BranchController@logout');
	Route::get('/branch/branch_workingtime/{id?}', 'branch\BranchController@branch_workingtime');
	Route::get('/branch/delete_timeslot/{id?}', 'branch\BranchController@delete_timeslot');
	Route::post('/branch/update_branch_workingtime', 'branch\BranchController@update_branch_workingtime');
	
	/************ Deliveryboy Controller *******************/
	
	Route::get('/branch/deliveryboys', 'branch\DeliveryboyController@getdeliveryboys');
	Route::get('/branch/adddeliveryboy', 'branch\DeliveryboyController@adddeliveryboy_form');
	Route::post('/branch/adddeliveryboy', 'branch\DeliveryboyController@adddeliveryboy');
	Route::get('/branch/editdeliveryboy/{id?}', 'branch\DeliveryboyController@getdeliveryboy');
	Route::post('/branch/updatedeliveryboy', 'branch\DeliveryboyController@updatedeliveryboy');
	Route::get('/branch/change_deliveryboystatus', 'branch\DeliveryboyController@change_deliveryboystatus');
	Route::get('/branch/filterdeliveryboy', 'branch\DeliveryboyController@filterdeliveryboys');
	Route::get('/branch/deletedeliveryboy/{id?}', 'branch\DeliveryboyController@deletedeliveryboy');
	Route::get('/branch/restoredeliveryboy/{id?}', 'branch\DeliveryboyController@restoredeliveryboy');
	
	/************* Staff Controller *********************/
	
	Route::get('/branch/staffs', 'branch\StaffController@getstaffs');
	Route::get('/branch/addstaff', 'branch\StaffController@addstaff_form');
	Route::post('/branch/addstaff', 'branch\StaffController@addstaff');
	Route::get('/branch/change_staffstatus', 'branch\StaffController@change_staffstatus');
	Route::get('/branch/getstaff/{id?}', 'branch\StaffController@getstaff');
	Route::post('/branch/updatestaff', 'branch\StaffController@updatestaff');
	Route::get('/branch/deletestaff/{id?}', 'branch\StaffController@deletestaff');
	Route::get('/branch/filterstaffs', 'branch\StaffController@filterstaffs');
	
	/******************* Order Controller ********************/
	
	Route::get('/branch/orders', 'branch\OrderController@getorders');
	Route::get('/branch/createorder', 'branch\OrderController@createorder_form');
	Route::get('/branch/getcustomer', 'branch\OrderController@getcustomer');
	Route::post('/branch/createorder', 'branch\OrderController@createorder');
	Route::get('/branch/getbranch_deliveryboys', 'branch\OrderController@getbranch_deliveryboys');
	Route::get('/branch/getbranch_delivery', 'branch\OrderController@getbranch_delivery');
	Route::get('/branch/selectingredient/{id?}', 'branch\OrderController@selectingredient');
	Route::get('/branch/getcategory_items', 'branch\OrderController@getcategory_items');
	Route::get('/branch/addtocart', 'branch\OrderController@addtocart');
	Route::get('/branch/add_remove_quantity', 'branch\OrderController@add_remove_quantity');
	Route::get('/branch/delete_cartitem', 'branch\OrderController@delete_cartitem');
	Route::get('/branch/additem', 'branch\OrderController@additem');
	Route::get('/branch/editorder/{id}', 'branch\OrderController@editorder');
	Route::post('/branch/updateorder', 'branch\OrderController@updateorder');
	Route::get('/branch/updateqty', 'branch\OrderController@updateqty');
	Route::get('/branch/delete_item', 'branch\OrderController@delete_item');
	Route::post('/branch/getorder_branches', 'branch\OrderController@getorder_branches');
	Route::post('/branch/update_orderstatus', 'branch\OrderController@update_orderstatus');
	Route::get('/branch/assign_order/{id?}', 'branch\OrderController@assign_order');
	Route::get('/branch/assign_deliveryboy/{id?}/{order_id?}', 'branch\OrderController@assign_deliveryboy');	
	
});


/*************** Front End************************/

Route::post('/register', 'Auth\AuthController@register');
Route::post('/login', 'Auth\AuthController@login');
Route::get('/forgotpassword', 'Auth\AuthController@forgotpassword');
Route::get('verification/{key?}', 'Auth\AuthController@verification');
Route::get('registration_verification/{key?}', 'Auth\AuthController@registration_verification');
Route::post('registration_otpverification', 'Auth\AuthController@verifiotp');
Route::get('registration_resendotp', 'Auth\AuthController@resendotp');

Route::get('/', 'HomeController@index');
Route::get('/changelanguage/{language?}', 'HomeController@changelanguage');
Route::get('/order-assign-dook', 'HomeController@orderAssignToDook');
Route::post('/dook-order-status', 'HomeController@updateDookOrderStatus');
Route::post('/foodics-order-status', 'HomeController@updateFoodicsOrderStatus');

Route::get('/logout', 'UserController@logout');
Route::post('/getitems', 'ShopController@listings');
Route::get('/listings', 'ShopController@getitems');
Route::get('/edit_profile', 'UserController@profiledetails');
Route::post('/updateuser', 'UserController@updateuser');
Route::get('/address_book', 'UserController@address_book');
Route::post('/addaddress', 'UserController@add_address');
Route::get('/delete_address', 'UserController@delete_address');
Route::get('/addaddress_popup', 'UserController@addaddress_popup');
Route::post('/updateaddress', 'UserController@updateaddress');
Route::get('/myorder', 'UserController@myorder');
Route::get('/getorder', 'UserController@getorder');
Route::get('/update_address/{address_key?}/{order_key?}', 'UserController@update_address');
Route::get('/update_orderaddress', 'UserController@update_orderaddress');
Route::get('/rate-driver', 'UserController@rateDriver');
Route::post('/addrating', 'UserController@addRating');


Route::get('/app', 'UserController@downloadapp');

Route::get('/selectingredient', 'ShopController@selectingredient');
Route::get('/addtocart', 'ShopController@addtocart');
Route::get('/additem', 'ShopController@additem');
Route::get('/update_cart', 'ShopController@update_cart');
Route::get('/remove_product/{id}', 'ShopController@remove_product');
Route::get('/cart', 'ShopController@cart');
Route::post('/getdelivery_time', 'ShopController@getdelivery_time');
Route::get('/changedefault_address', 'ShopController@changedefault_address');
Route::get('/checkout', 'ShopController@checkout');
Route::get('/verify_otp', 'ShopController@verification');
Route::get('/saveaddress', 'ShopController@saveaddress');
Route::post('/payment', 'ShopController@payment');
Route::get('/resendotp', 'ShopController@resendotp');
Route::post('/verifyotp', 'ShopController@verifyotp');
Route::get('/autocomplete_branch', 'ShopController@autocomplete_branch');

Route::get('/payfort', 'PaymentController@payfort');
Route::post('/getpaymentpage/{request?}', 'PaymentController@processRequest');
Route::get('/payment_response', 'PaymentController@payment_response');



/*************** Api ****************************/

Route::group(['prefix' => 'api/v1'], function(){
    Route::post('/signup', 'ApiController@signup');
	Route::post('/login', 'ApiController@login');
	Route::get('/resendotp_register', 'ApiController@resendotp_register');
	Route::post('/verifyotp_register', 'ApiController@verifyotp_register');
	Route::get('/verifyaccount/{key?}', 'ApiController@verifyaccount');
	Route::post('/getcategory_list', 'ApiController@getcategory_list');
	Route::get('/getuser', 'ApiController@getuser');
	Route::get('/getitem', 'ApiController@getitem');
	Route::get('/getingredients', 'ApiController@getingredients');
	Route::post('/calculate_basket', 'ApiController@calculate_basket');
	Route::get('/get_addressbooks', 'ApiController@get_addressbooks');
	Route::post('/add_addressbook', 'ApiController@add_addressbook');
	Route::get('/get_addressbook', 'ApiController@get_addressbook');
	Route::post('/update_addressbook', 'ApiController@update_addressbook');
	Route::get('/delete_address', 'ApiController@delete_address');
	Route::post('/changedefault_address', 'ApiController@changedefault_address');
	Route::get('/getdatetime', 'ApiController@getdatetime');
	Route::get('/verify_branch_availability', 'ApiController@verify_branch_availability');
	Route::get('/myorders', 'ApiController@myorders');
	Route::post('/updateuser', 'ApiController@updateuser');
	Route::post('/changepassword', 'ApiController@changepassword');
	Route::post('/forgetpassword', 'ApiController@forgetpassword');
	Route::get('/getorder', 'ApiController@getorder');
	Route::post('/customer_info', 'ApiController@customer_info');
	Route::post('/payment', 'ApiController@payment');
	Route::get('/resendotp', 'ApiController@resendotp');
	Route::post('/verifyotp', 'ApiController@verifyotp');
	Route::get('/payment_response', 'ApiController@payment_response');
	Route::post('/contact_us', 'ApiController@contact_us');
	Route::post('/promocode', 'ApiController@promocode');
	Route::get('/getlanguages', 'ApiController@getlanguages');
	Route::post('/getbranches', 'ApiController@getbranches');
	Route::get('/trackorder', 'ApiController@trackorder');
	Route::get('/order_status', 'ApiController@getorder_status');
	Route::post('/add-rating', 'ApiController@addRating');
});

/*************** Delivery boy Api ****************************/

Route::group(['prefix' => 'api/v1/deliveryboy'], function(){
    Route::post('/login', 'DeliveryboyapiController@login');
    Route::post('/forgot_password', 'DeliveryboyapiController@forgetpassword');
    Route::get('/neworders', 'DeliveryboyapiController@getneworders');
    Route::get('/assignedorders', 'DeliveryboyapiController@getassignorders');
    Route::get('/deliveredorders', 'DeliveryboyapiController@getdelivered_orders');
    Route::get('/declinedorders', 'DeliveryboyapiController@getcancel_orders');
    Route::get('/order_details', 'DeliveryboyapiController@getorder');
    Route::post('/accept_order', 'DeliveryboyapiController@acceptorder');
    Route::post('/update_order_status', 'DeliveryboyapiController@pickuporder');
    Route::post('/cancel_order', 'DeliveryboyapiController@cancelorder');
    Route::post('/completeorder', 'DeliveryboyapiController@completeorder');
    Route::post('/update_deliveryboy_status', 'DeliveryboyapiController@update_deliveryboy_status');
    Route::post('/edit_profile', 'DeliveryboyapiController@updateprofile');
    Route::post('/change_password', 'DeliveryboyapiController@change_password');
    Route::get('/logout', 'DeliveryboyapiController@logout');
    Route::get('/sendMessage', 'DeliveryboyapiController@sendMessage');
});



/*
Route::get('/admin/notification/auto', function () {
    return view('admin/notificationauto');
});

Route::get('/admin/notification/send', function () {
    return view('admin/notificationsend');
});*/

Route::get('/admin/notification/auto', 'admin\InotificationController@notificationSave');
Route::post('/admin/notification/auto', 'admin\InotificationController@notificationSave');

Route::get('/admin/notification/send', 'admin\InotificationController@notificationSend');
Route::post('/admin/notification/send', 'admin\InotificationController@notificationSend');