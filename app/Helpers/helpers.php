<?php

use App\Attendance_status;
use App\FloorTable;
use App\OrderPayment;
use App\StoreCustomer;
use Illuminate\Support\Facades\Auth;
use App\Country;
use App\Items;
use App\Categories;
use App\User;
use App\Regions;
use App\Ad;
use App\LastViewed;
use App\Watchlist;
use App\Item_answers;
use App\Currency;
use App\Store;
use App\Supplier;
use App\Tax_rates;
use App\Product_images;
use App\Variant;
use App\Product;
use App\Company_setting;
use App\Order;
use App\Customer;
use App\Shipping_option;
use App\Product_variant;
use App\Store_products;
use App\Stock;
use App\Email_template;
use App\Sync;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

if (! function_exists('isActiveRoute')) {

    function isActiveRoute($route, $output = "active")
    {
        if (Route::current()->uri == $route) return $output;
    }
}

if (! function_exists('setActive')) {

    function setActive($paths,$class = TRUE)
    {
        foreach ($paths as $path) {

            if(Request::is($path . '*')){
                if($class)
                    return ' class=active';
                else
                    return ' active';
            }

        }

    }
}

if (! function_exists('checkImage')) {

    function checkImage($path, $size=false)
    {
        ///print_r(public_path('uploads/'.$path).'<br>');
        if (file_exists(public_path('uploads/'.$path))){
            return asset('uploads/'.$path);
        }

        else
            if($size)
                return asset('uploads/no_image.png');
            else
                return asset('uploads/thumbs/no_image.png');
    }
}

if (! function_exists('areActiveRoutes')) {

    function areActiveRoutes(Array $routes, $output = "active")
    {
        foreach ($routes as $route)
        {
            if (Route::current()->uri == $route) return $output;
        }

    }

}

if (! function_exists('settingValue')) {

    function settingValue($key)
    {

        $setting = \DB::table('site_settings')->where('key',$key)->first();

        if($setting)
            return $setting->value;
        else
            return '';
    }
}

if (! function_exists('companySettingValue')) {

    function companySettingValue($column)
    {
        if(Auth::guard('company')->check())
            $setting = Company_setting::where('company_id',Auth::id())->first();
        elseif(Auth::guard('api')->check()){
            $setting = Company_setting::where('company_id',getComapnyIdByUser())->first();
        }

        if($setting)
            return $setting->{$column};
        else
            return '';
    }
}

if (! function_exists('companySettingValueApi')) {

    function companySettingValueApi($column,$company_id = 0)
    {
        if($company_id > 0){
            $setting = Company_setting::where('company_id',$company_id)->first();
        } else {
            $setting = Company_setting::where('company_id',getComapnyIdByUser())->first();
        }
        if($setting)
            return $setting->{$column};
        else
            return '';
    }
}

if (! function_exists('companySettingValueByCompanyIdApi')) {

    function companySettingValueByCompanyIdApi($column,$comapny_id)
    {
        $setting = Company_setting::where('company_id',$comapny_id)->first();
        if($setting)
            return $setting->{$column};
        else
            return '';
    }
}

if (! function_exists('getCountries')) {

    function getCountries()
    {
        return Country::pluck('name', 'code')->prepend('Select Country','');
    }
}


if (! function_exists('getCompanyTypes')) {

    function getCompanyTypes()
    {
        $arr = [];
        $arr[0] = 'Retailer';
        $arr[1] = 'Hospitality';
        $data = collect($arr);
        $data = $data->prepend('Select Company Type','');
        return $data;
        ///return Country::pluck('name', 'code')->prepend('Select Country','');
    }
}


if (! function_exists('getParentCategoriesDropdown')) {

    function getParentCategoriesDropdown()
    {
        return Categories::where('parent_id', 0)->pluck('category_name', 'id')->prepend('Root Category',0);
    }
}

if (! function_exists('getCurrencyDropdown')) {

    function getCurrencyDropdown()
    {
        return Currency::where('company_id',Auth::id())->pluck('name', 'id')->prepend('Select Currency','');
    }
}

if (! function_exists('getCategoriesDropdown')) {

    function getCategoriesDropdown()
    {
        $store_ids = Store::where('company_id',Auth::id())->pluck('id');

        return Categories::whereIn('store_id',$store_ids)->pluck('category_name', 'id')->prepend('Select Categories','');

    }
}

if (! function_exists('getStoreIds')) {

    function getStoreIds()
    {

        if(Auth::guard('company')->check())
            $store_ids = Store::where('company_id',Auth::id())->pluck('id');
        elseif(Auth::guard('api')->check()){
            $store_ids = [Auth::user()->store_id];
        }

        return $store_ids;

    }
}

if (! function_exists('updateOrderProductsStock')) {

    function updateOrderProductsStock($order_id, $user_id=0 , $customer_id = 0)
    {
        //customer login check
        if($customer_id > 0){
            $user_id = $customer_id;
        } else {
            if($user_id==0){
                $user_id = Auth::id();
            }
        }

        $order = Order::find($order_id);

        if($order){
            $products = json_decode($order->order_items);

            if(count($products)>0){
                foreach($products as $product){

                    $store_product = Store_products::where('product_id',$product->item_id)->where('store_id',$order->store_id)->first();
                    if($store_product){
                        updateProductStockByData($store_product->product_id, $store_product->store_id, $product->quantity, 2, 3, $order->id, $user_id, $order->order_note);

                        // item combos
                        if(!empty($product->item_combos)){
                            $item_combos = json_decode($product->item_combos);
                            if(count($item_combos)>0){
                                foreach($item_combos as $item_combo){
                                    $store_product = Store_products::where('product_id',$item_combo->id)->where('store_id',$order->store_id)->first();
                                    if($store_product){
                                        updateProductStockByData($store_product->product_id, $store_product->store_id, 1, 2, 3, $order->id, $user_id, $order->order_note);
                                    }
                                }
                            }
                        }

                        // item modifiers
                        if(!empty($product->item_modifiers)){
                            $item_modifiers = json_decode($product->item_modifiers);
                            if(count($item_modifiers)>0){
                                foreach($item_modifiers as $item_modifier){
                                    $store_product = Store_products::where('product_id',$item_modifier->id)->where('store_id',$order->store_id)->first();
                                    if($store_product){
                                        updateProductStockByData($store_product->product_id, $store_product->store_id, 1, 2, 3, $order->id, $user_id, $order->order_note);
                                    }
                                }
                            }
                        }

                    }
                }
            }

        }
    }

}

if (! function_exists('getStoresDropdown')) {

    function getStoresDropdown()
    {
        return Store::where('company_id',Auth::id())->pluck('name', 'id')->prepend('Select Store','');
    }
}

if (! function_exists('getMediaDropdown')) {

    function getMediaDropdown()
    {
        $media['image'] = 'Image';
        $media['video'] = 'Video';
        return $media;
    }
}

if (! function_exists('getStoreDropdownHtml')) {

    function getStoreDropdownHtml()
    {
        // if(isset(Request::segment(4)))

        $stores = Store::where('company_id',Auth::id())->pluck('name', 'id');


        $html = '<span class="pull-right col-lg-3" style="margin-top: -5px;">';
        $html .= '<select class="form-control select2" id="store_reports"> ';
        $html .= '<option value="">All Stores</option> ';

        foreach($stores as $key => $value){
            $html .= '<option value="'. Hashids::encode($key) .'" '.(Request::segment(4)== Hashids::encode($key)?'selected':'').'>'. $value .'</option> ';
        }

        $html .= '</select> ';

        return $html;
    }
}

if (! function_exists('getStoresFilterDropdown')) {

    function getStoresFilterDropdown()
    {
        return Store::where('company_id',Auth::id())->pluck('name', 'id')->prepend('Filter by store','');
    }
}

if (! function_exists('getProductsDropdown')) {

    function getProductsDropdown()
    {
        return Product::where('company_id',Auth::id())->where('product_id',0)->pluck('name', 'id')->prepend('Select Product','');
    }
}

if (! function_exists('getSelectedProduct')) {

    function getSelectedProduct($product_id)
    {
        return Product::where('id',$product_id)->pluck('name', 'id');
    }
}

if (! function_exists('getVariants')) {

    function getVariantsDropdown()
    {
        return Variant::with(['company'])->company(Auth::id())->pluck('name', 'id')->prepend('Select attribute','');
    }
}

if (! function_exists('getSuppliersDropdown')) {

    function getSuppliersDropdown()
    {
        return Supplier::pluck('name', 'id')->prepend('Select Supplier','');
    }
}

if (! function_exists('getTaxRatesDropdown')) {

    function getTaxRatesDropdown()
    {
        return Tax_rates::where('company_id',Auth::id())->orderBy('id','asc')->pluck('name', 'id');
    }
}

if (! function_exists('getShippingOptionsDropdown')) {

    function getShippingOptionsDropdown()
    {
        return Shipping_option::where('company_id',Auth::id())->orderBy('cost','asc')->pluck('name', 'id');
    }
}

if (! function_exists('getRegions')) {

    function getRegions()
    {
        return Regions::get();
    }
}

if (! function_exists('getActiveLeagues')) {

    function getActiveLeagues($user_id)
    {
        $category_ids = Items::select('category_id')->where(['user_id' => $user_id])->groupBy('category_id')->pluck('category_id');
        $active_leagues = Categories::whereIn('id', $category_ids)->groupBy('parent_id')->count();
        $active_leagues = str_pad(($active_leagues),7,0,STR_PAD_LEFT);
        return $active_leagues;
    }
}

if (! function_exists('getActiveLeaguesByUserId')) {

    function getActiveLeaguesByUserId($user_id)
    {
        $category_ids = Items::select('category_id')->where(['user_id' => $user_id])->groupBy('category_id')->pluck('category_id');
        $active_leagues_ids = Categories::select('parent_id')->whereIn('id', $category_ids)->groupBy('parent_id')->pluck('parent_id');
        $active_leagues = Categories::whereIn('id', $active_leagues_ids)->get();

        return $active_leagues;
    }
}

if (! function_exists('getActiveLeaguesList')) {

    function getActiveLeaguesList()
    {
        $category_ids = Items::select('category_id')->groupBy('category_id')->pluck('category_id');
        $active_leagues = Categories::with('category')->whereIn('id', $category_ids)->groupBy('parent_id')->get();

        return $active_leagues;
    }
}

if (! function_exists('getPositionInLeagues')) {

    function getPositionInLeagues($record_id)
    {
        $item_id = Items::find($record_id)->category_id;

        $query = "SELECT id, score,deleted_at, FIND_IN_SET( score, 
                        (SELECT GROUP_CONCAT( score ORDER BY score DESC ) FROM items )
                    ) AS position
                    FROM items
                    WHERE category_id = ".$item_id." and id = ".$record_id."  and deleted_at is null ";

        $position = DB::select( DB::raw($query) );

        if(!empty($position)){
            $position = $position[0]->position;
        }else{
            $position = 0;
        }

        return str_pad(($position),7,0,STR_PAD_LEFT);

    }
}

if (! function_exists('getPositionInLeaguesByLeagueIdAndUserId')) {

    function getPositionInLeaguesByLeagueIdAndUserId($category_id,$user_id)
    {
        $item_ids = Categories::select('id')->where('parent_id', $category_id)->pluck('id');
        $item_ids = implode(',',$item_ids->toArray());

        $query = "SELECT id, score,user_id,deleted_at, FIND_IN_SET( score, 
                        (SELECT GROUP_CONCAT( score ORDER BY score DESC ) FROM items where user_id = ".$user_id." and deleted_at is null)
                    ) AS position
                    FROM items
                    WHERE category_id IN (".$item_ids.") and user_id = ".$user_id."  and deleted_at is null ";

        $position = DB::select( DB::raw($query) );

        if(!empty($position)){
            $position = $position[0]->position;
        }else{
            $position = 0;
        }


        return $position;
    }
}

if (! function_exists('registeredUsers')) {

    function registeredUsers()
    {
        $user = User::count();
        return number_format($user);
    }
}

if (! function_exists('allRecords')) {

    function allRecords()
    {
        $records = Items::count();
        return number_format($records);
    }
}

if (! function_exists('get_ad_content')) {

    function getAdContent($id)
    {
        $resu = Ad::where('id', $id)->first();
        if($resu){

            $ad_detail = $resu->code;
            $base_url = \URL::to('/');
            $ad_content   =   str_replace('{asset}',$base_url,$ad_detail);
        }
        return $ad_content;
    }
}
if (! function_exists('AddToLastViewed')) {

    function AddToLastViewed($user_id, $item_id)
    {
        $input['user_id'] =  $user_id;
        $input['item_id'] =  $item_id;

        $last_viewed = LastViewed::firstOrNew($input);
        $last_viewed->updated_at = date('Y-m-d G:i:s');
        $last_viewed->save();
    }
}

if (! function_exists('GetLastViewed')) {

    function GetLastViewed()
    {
        if(Auth::id()){
            $user_id =  Auth::id();
            $GetLastViewed = LastViewed::with(['item.category.region','item.record_images'])->where(['user_id' => $user_id])->orderBy('updated_at','desc')->take(5)->get();
            $GetLastViewed->map(function ($item) {

                $item->item->record_images->map(function ($image) {
                    $image['record_thumbnail'] = checkImage('items/thumbs/'. $image->name);
                    $image['record_image'] = checkImage('items/'. $image->name);
                });

                return $item;
            });
        }else{
            $GetLastViewed = [];
        }
        return $GetLastViewed;
    }
}

if (! function_exists('updateUserScore')) {

    function updateUserScore($user_id)
    {
        $score = Items::select('score')->where('user_id' , $user_id)->pluck('score');
        $user_score = str_pad($score->sum(),7,0,STR_PAD_LEFT);
        $update_score['user_score'] = $user_score;
        $user = User::findOrFail($user_id);
        $user->update($update_score);
    }
}

if (! function_exists('getProductDefaultImage')) {

    function getProductDefaultImage($product_id, $size=false)
    {

        $product_images = Product_images::where('product_id',$product_id);

        if($product_images->count()>0){
            $product_image = $product_images->where('default',1)->first();

            if(!$product_image)
                $product_image = Product_images::where('product_id',$product_id)->first();

            if($size)
                return checkImage('products/'.$product_image->name);
            else
                return checkImage('products/thumbs/'.$product_image->name);
        }else{
            checkImage('products/no-image.jpg');
        }

    }

}

if (! function_exists('totalSales')) {

    function totalSales()
    {
        $store_ids = Store::where('company_id',Auth::id())->pluck('id');

        $orders = Order::whereIn('store_id',$store_ids)->count();
        return number_format($orders);

    }

}

if (! function_exists('totalStores')) {

    function totalStores()
    {

        $stores = Store::where('company_id',Auth::id())->count();
        return number_format($stores);

    }

}

if (! function_exists('totalSuppliers')) {

    function totalSuppliers()
    {

        $supplier = Supplier::count();
        return number_format($supplier);

    }

}

if (! function_exists('totalUsers')) {

    function totalUsers()
    {

        $store_ids = Store::where('company_id',Auth::id())->pluck('id');

        $users = User::whereIn('store_id',$store_ids)->count();

        return number_format($users);

    }

}

if (! function_exists('totalCategories')) {

    function totalCategories()
    {
        $store_ids = Store::where('company_id',Auth::id())->pluck('id');

        $categories = Categories::whereIn('store_id',$store_ids)->count();

        return number_format($categories);

    }

}
if (! function_exists('totalProducts')) {

    function totalProducts()
    {

        $products = Product::with(['company'])->where('product_id',0)->company(Auth::id())->count();

        return number_format($products);

    }

}

if (! function_exists('totalCustomers')) {

    function totalCustomers()
    {

        //$customers = Customer::where('company_id',Auth::id())->count();
        $customers = StoreCustomer::where('company_id',Auth::id())->orderBy('id','desc')->count();

        return number_format($customers);

    }

}

if (! function_exists('getComapnyIdByUser')) {

    function getComapnyIdByUser()
    {

        $user = User::with(['store.company'])->find(Auth::id());

        return $user->store->company->id;

    }

}

if (! function_exists('getVariantData')) {

    function getVariantData($product_id, $attribute_id, $return = 'id')
    {

        $product_variant = Product_variant::where('variant_product_id',$product_id)->where('product_attribute_id',$attribute_id)->first();

        if($product_variant){
            if($return == 'id')
                return $product_variant->id;
            else
                return $product_variant->name;
        }

        return '';

    }

}

if (! function_exists('getStoreProductsData')) {

    function getStoreProductsData($product_id, $store_id, $return = 'id')
    {

        $store_product = Store_products::where('product_id',$product_id)->where('store_id',$store_id)->first();

        if($store_product){
            if($return == 'id')
                return $store_product->id;
            else
                return $store_product->quantity;
        }

        return 0;

    }

}

if (! function_exists('updateProductStock')) {

    function updateProductStock($product_id, $store_id)
    {
        $stocks = Stock::where('product_id',$product_id)->where('store_id',$store_id)->get();

        if($stocks){
            $quantity = 0;

            foreach($stocks as $stock){

                if($stock->stock_type == 1)
                    $quantity = $quantity + $stock->quantity;
                elseif($stock->stock_type == 2)
                    $quantity = $quantity - $stock->quantity;
            }
            $stock_product = Store_products::where('product_id',$product_id)->where('store_id',$store_id)->first();
            if(!$stock_product){
                $stock_product = new Store_products();
                $stock_product->product_id = $product_id;
                $stock_product->store_id = $store_id;
                $stock_product->quantity = $quantity;
                $stock_product->low_stock = 0;
                $stock_product->low_stock_status = 0;
                $stock_product->email_status = 0;
                $stock_product->save();
            }
            $stock_product->quantity = $quantity;
            $stock_product->save();

            if($stock_product->quantity >= $stock_product->low_stock){
                $stock_product->email_status = 0;
                $stock_product->save();
            }
//            if($stock_product->quantity <= $stock_product->low_stock){
//                $stock_product->email_status = 1;
//                $stock_product->save();
//            }
            //sending low stock email
            if($stock_product->quantity <= $stock_product->low_stock && $stock_product->email_status == 0){

                $store = Store::find($store_id);
                $company = $store->company;
                $product = Product::find($product_id);
                $logo = checkImage('uploads/companies/'. $company->logo);
                $email_data = Email_template::where('name','Low Stock')->where('company_id',$company->id)->first();
                $logo_image = "<img src='$logo'>";
                //$site_link = '<a href="'.url('company/login').'">Click here for login</a>';
                $email_to            = $company->email;
                $email_from            = $email_data->from;
                $email_subject            = $email_data->subject;
                $email_body          = $email_data->content;
                $email_body = str_replace('{company}',$company->name,$email_body);
                $email_body = str_replace('{product_name}',$product->name,$email_body);
                $email_body = str_replace('{product_code}',$product->code,$email_body);
                $email_body = str_replace('{product_stock}',$stock_product->quantity,$email_body);
                //$email_body = str_replace('{logo}',$logo_image,$email_body);
                $email_body = str_replace('{site_name}',settingValue('site_title'),$email_body);
                $body = $email_body;

                //send email to supplier if exists
                //Log::info(['product' => $product]);
                $supplier = DB::table('suppliers')->where('id',$product->supplier_id)->first();
                //Log::info(['supplier' => $supplier]);
                if($supplier != null){
                    //Log::info('supplier exitSTS');
                    if(isset($supplier->email)){
                        $res['final_content'] = $body;
                        try {
                            $store = Store::find($store_id);
                            if($store){
                                $username = $store->user_name;
                                $password = $store->password;
                                $host = $store->host;
                                $backup = \Mail::getSwiftMailer();
                                if(isset($username) && isset($password) && isset($host)){
                                    $transport = (new \Swift_SmtpTransport($host, '587'))
                                        ->setUsername($username)
                                        ->setPassword($password)
                                        ->setEncryption('tls');

                                    \Mail::setSwiftMailer(new \Swift_Mailer($transport));
                                    try {
                                        Mail::send('emails.email_body',$res, function ($message) use ($email_data, $email_to,$supplier) {
                                            $message->from('info@skulocity.com', $email_data->name);
                                            $message->to($supplier->email, $supplier->email)
                                                ->cc($email_to)
                                                ->subject($email_data->subject);
                                        });
                                    }catch (\Exception $e) {
                                        //echo $e->getMessage();
                                    }
                                } else {
                                    Mail::setSwiftMailer($backup);
                                    try {
                                        Mail::send('emails.email_body',$res, function ($message) use ($email_data, $email_to,$supplier) {
                                            $message->from('info@skulocity.com', $email_data->name);
                                            $message->to($supplier->email, $supplier->email)
                                                ->cc($email_to)
                                                ->subject($email_data->subject);
                                        });
                                    }catch (\Exception $e) {
                                        //echo $e->getMessage();
                                    }
                                }
                            }
                            Mail::send('emails.email_body',$res, function ($message) use ($email_data, $email_to,$supplier) {
                                $message->from('info@skulocity.com', $email_data->name);
                                $message->to($supplier->email, $supplier->email)
                                    ->cc($email_to)
                                    ->subject($email_data->subject);
                            });
                        }catch (\Exception $e) {
                            //echo $e->getMessage();
                        }
                    } else {
                        Email_template::sendEmail($email_to,$email_data,$body,$store_id);
                    }
                }
                else {
                    Email_template::sendEmail($email_to,$email_data,$body,$store_id);
                }



                $stock_product->email_status = 1;
                $stock_product->save();
            }

            if($stock_product){
                $stock_product_data = $stock_product->first();
                if($stock_product_data->quantity<1){
                    //$stock_product->delete();
                }
            }
        }

    }

}

if (! function_exists('getEmailHtml')) {

    function getEmailHtml()
    {
        $html = '
Product out of stock<br />
<br />
Hello {company},<br />
<br />
Your product {product_name} , code ({product_code}) is running out of stock. Current product stock is {product_stock}<br />
<br />
Best regards,<br />
{site_name}</h3>';
        return $html;
    }

}

if (! function_exists('updateProductStockByData')) {

    function updateProductStockByData($product_id, $store_id, $quantity, $stock_type, $origin, $order_id = 0, $user_id = 0, $note = NULL)
    {
        if($quantity>0){
            $store_data['order_id'] = $order_id;
            $store_data['product_id'] = $product_id;
            $store_data['store_id'] = $store_id;
            $store_data['user_id'] = $user_id;
            $store_data['quantity'] = $quantity;
            $store_data['stock_type'] = $stock_type;
            $store_data['origin'] = $origin;
            $store_data['note'] = $note;

            $stock = Stock::create($store_data);
            if($stock)
                updateProductStock($product_id, $store_id);
        }

    }

}

if (! function_exists('find_key_value')) {

    function find_key_value($array, $key, $val)
    {
        foreach ($array as $item)
        {
            if (is_array($item) && find_key_value($item, $key, $val)) return true;

            if (isset($item[$key]) && $item[$key] == $val) return true;
        }

        return false;
    }

}

if (! function_exists('getProductTagline')) {

    function getProductTagline($product_id, $modifiers="", $combos="")
    {
        $tagline = '';
        $product = Product::with(['product_variants.product_attribute.variant'])->find($product_id);
        if($product){
            if($product->product_id>0){
                foreach($product->product_variants as $key => $product_variant){
                    if($key==0){
                        if(isset($product_variant->product_attribute)){
                            $tagline .= $product_variant->name .' '.$product_variant->product_attribute->variant->name;
                        }

                    }

                    else
                        $tagline .= ', '.$product_variant->name .' '.$product_variant->product_attribute->variant->name;

                }
            }

            if(!empty($modifiers)){
                $modifiers = json_decode($modifiers);
                foreach($modifiers as $key => $modifier){
                    if($key==0)
                        $tagline .= ' with '.$modifier->name;
                    else
                        $tagline .= ', '.$modifier->name;
                }
            }

            if(!empty($tagline))
                $tagline = '<p>'.$tagline.'</p>';

            if(!empty($combos)){
                $tagline .= '<ul>';
                $combos = json_decode($combos);
                foreach($combos as $combo){
                    $tagline .= '<li>'.$combo->name.'     <span><b>Code</b>: &nbsp;&nbsp;&nbsp;'. $combo->code .'&nbsp;&nbsp; <b>SKU</b>: &nbsp;&nbsp;&nbsp;'. $combo->sku .'</span></li>';
                }
            }
        }


        return $tagline;



    }

}

if (! function_exists('updateSyncData')) {

    function updateSyncData($type,$id,$store_ids=[])
    {
        // type product,delete_product,category,delete_category,customer,delete_customer,order

        if($type=='product'){

            $product = Product::with(['company.store'])->find($id);
            if($product){
                //dd($product->company->store->toArray());
                foreach ($product->company->store as $store) {
                    $syncData = [];
                    $syncData['sync_id'] =  $product->id;
                    $syncData['store_id'] =  $store->id;
                    $syncData['sync_type'] =  'delete_product';
                    $store_product = Store_products::where('product_id',$product->id)->where('store_id',$store->id)->first();
                    if($store_product && $store_product->quantity>0){
                        $syncData['sync_type'] =  'product';
                    }

                    Sync::create($syncData);
                }
            }
        }
        elseif($type=='delete_product'){
            foreach($store_ids as $store_id){
                $syncData = [];
                $syncData['sync_id'] =  $id;
                $syncData['store_id'] =  $store_id;
                $syncData['sync_type'] =  'delete_product';
                Sync::create($syncData);
            }
        }
        elseif($type=='store'){
            $syncData = [];
            $syncData['sync_id'] =  $id;
            $syncData['store_id'] =  $id;
            $syncData['sync_type'] =  'store';
            Sync::create($syncData);
        }
        elseif($type=='category'){

            $category = Categories::find($id);
            if($category){

                if($category->parent_id==0){
                    $category_id = $category->id;
                }else{
                    $category_id = $category->parent_id;
                }

                $syncData['sync_id'] =  $category_id;
                $syncData['store_id'] =  $category->store_id;
                $syncData['sync_type'] =  'category';
                Sync::create($syncData);
            }
        }
        elseif($type=='delete_category'){

            foreach($store_ids as $store_id){
                $syncData = [];
                $syncData['sync_id'] =  $id;
                $syncData['store_id'] =  $store_id;
                $syncData['sync_type'] =  'delete_category';
                Sync::create($syncData);
            }
        }
        elseif($type=='customer'){

            $customer = Customer::find($id);
            if($customer){

                $store_customers = StoreCustomer::where('customer_id',$customer->id)->get();
                foreach ($store_customers as $store_customer){
                    $syncData['sync_id'] =  $store_customer->customer_id;
                    $syncData['store_id'] =  $store_customer->store_id;
                    $syncData['sync_type'] =  'customer';
                    Sync::create($syncData);
                }


            }
        }
        elseif($type=='clock_in'){

            $attendance = Attendance_status::find($id);
            if($attendance){
                $user = User::find($attendance->user_id);
                $syncData['sync_id'] =  $attendance->id;
                $syncData['store_id'] =  $user->store->id;
                $syncData['sync_type'] =  'clock_in';
                Sync::create($syncData);
            }
        }
        elseif($type=='clock_out'){
            $attendance = Attendance_status::find($id);
            if($attendance){
                $user = User::find($attendance->user_id);
                $syncData['sync_id'] =  $attendance->id;
                $syncData['store_id'] =  $user->store->id;
                $syncData['sync_type'] =  'clock_out';
                Sync::create($syncData);
            }
        }
        elseif($type=='discount'){
            $discount = \App\Discount::find($id);
            if($discount){
                $syncData['sync_id'] =  $id;
                $syncData['store_id'] =  $store_ids;
                $syncData['sync_type'] =  'discount';
                Sync::create($syncData);
            }
        }
        elseif($type=='discount_bogo'){
            $discount = \App\Discount::find($id);
            if($discount){
                $syncData['sync_id'] =  $id;
                $syncData['store_id'] =  $store_ids;
                $syncData['sync_type'] =  'discount_bogo';
                Sync::create($syncData);
            }
        }
        elseif($type=='discount_delete'){
            $discount = \App\Discount::find($id);
            if($discount){
                $syncData['sync_id'] =  $id;
                $syncData['store_id'] =  $store_ids;
                $syncData['sync_type'] =  'discount_delete';
                Sync::create($syncData);
            }
        }
        elseif($type=='ad'){
            $ad = Ad::find($id);
            if($ad){
                $syncData['sync_id'] =  $id;
                $syncData['store_id'] =  $store_ids;
                $syncData['sync_type'] =  'ad';
                Sync::create($syncData);
            }
        }
        elseif($type=='ad_delete'){
            $ad = Ad::find($id);
            if($ad){
                $syncData['sync_id'] =  $id;
                $syncData['store_id'] =  $store_ids;
                $syncData['sync_type'] =  'ad_delete';
                Sync::create($syncData);
            }
        }
        elseif($type=='table'){
            $table = FloorTable::where('table_id',$id)->first();
            if($table){
                $syncData['sync_id'] =  $id;
                $syncData['store_id'] =  $store_ids;
                $syncData['sync_type'] =  'table';
                Sync::create($syncData);
            }
        }
        elseif($type=='delete_customer'){

            foreach($store_ids as $store_id){
                $syncData = [];
                $syncData['sync_id'] =  $id;
                $syncData['store_id'] =  $store_id;
                $syncData['sync_type'] =  'delete_customer';
                Sync::create($syncData);
            }
        }
        elseif($type=='order'){

            $order = Order::find($id);
            if($order){
                $syncData['sync_id'] =  $order->id;
                $syncData['store_id'] =  $order->store_id;
                $syncData['sync_type'] =  'order';
                Sync::create($syncData);
            }
        }
    }

}

if (! function_exists('sendOrderEmail')) {

    function sendOrderEmail($order_id)
    {

        $order = Order::with(['store.currency'])->find($order_id);

        if($order){

            $email_data = Email_template::where('template_key','sale')->where('company_id',Auth::id())->first();
            if(!$email_data){
                $email_data = Email_template::where('template_key','sale')->where('company_id',0)->first();
            }
            $store = Store::find($order->store_id);
            $subject = $store->name.' '.'Order # '.$order->reference;
            $email_data->subject = $subject;
            $email_data->from = 'Order Confirmation';
            $email_data->name = 'Order Confirmation';

            $customer = Customer::find($order->customer);

            if($customer){

                $email_to = $customer->email;
                $email_body = $email_data->content;
                $store = Store::find($order->store_id);
                if($store){
                    $logo = '<img src="'. asset("stores/thumbs/".$store->image) .'" style="width:100%; max-width:300px;">';
                } else {
                    $logo = '<img src="'. asset("images/logo.png") .'" style="width:100%; max-width:300px;">';
                }

                $customer_info = '';
                if(!empty($customer->first_name))
                    $customer_info .= '<p>'. $customer->first_name.' '.$customer->last_name .'</p>';
                if(!empty($customer->address))
                    $customer_info .= '<p>Address: '. $customer->address.' '.$customer->city. ' ' .$customer->state .'</p>';
                if(!empty($customer->mobile))
                    $customer_info .= '<p>Phone: '. $customer->mobile .' </p>';

                $customer_info .= '<p>Email : '. $customer->email .' </p>';
                $currency_symbol = $order->store->currency->symbol;

                $order_products = ' <table cellpadding="0" cellspacing="0"><tr class="heading"><th align="center"> # </th>
                        <th align="left"> Item Description </th><th align="center"> Unit Cost </th><th align="center"> Quantity </th>
                        <th align="center">Discount</th><th align="center">Total</th></tr>';

                $item_array = json_decode($order->order_items);

                foreach($item_array as $key => $single_item){
                    $unit_price = $single_item->unit_price;

                    $item_modifiers = "";
                    if(isset($single_item->item_modifiers)){
                        $item_modifiers = $single_item->item_modifiers;
                        $modifiers = json_decode($item_modifiers);
                        foreach($modifiers as $modifier){
                            $unit_price = $unit_price+$modifier->price;
                        }
                    }

                    $item_sub_total = $unit_price * $single_item->quantity;
                    $item_discount = $single_item->item_discount;
                    $item_sub_total = $item_sub_total - $item_discount;
                    $item_combos = "";

                    if(isset($single_item->item_combos))
                        $item_combos = $single_item->item_combos;

                    if(isset($single_item->meal_type)){
                        $color = $single_item->meal_type->color;
                        $type = $single_item->meal_type->meal_type;
                      $meal_type_markup = '<span style="background-color: '.$color.'; font-size: 90%; float: right; font-weight: 700;line-height:1;white-space:nowrap;vertical-align:baseline;text-align: center;padding: 5px;border-radius: 3px;color: white; text-shadow: 2px 2px grey">'.$type.'</span>';
                    } else {
                        $meal_type_markup = '';
                    }

                    $order_products .= '<tr class="item" style="text-align: left;">
                            <td width="5%" align="center">'. ($key+1) .'</td>
                            <td width="50%" style="text-align: left;">
                            '.$meal_type_markup.'
                            '.$single_item->item_name.'<br/> <span style="color: #aeaeb1;font-style: italic;">'. getProductTagline($single_item->item_id,$item_modifiers,$item_combos) .'</span></td>
                            <td align="center">'. $currency_symbol . number_format($unit_price,2).'</td>
                            <td align="center">'. $single_item->quantity .'</td>
                            <td align="center">'. $currency_symbol . number_format($item_discount,2) .'</td>
                            <td align="center">'. $currency_symbol . number_format($item_sub_total,2) .'</td>
                        </tr>';
                }

                $order_products .= '</table>';

                $biller_detail = json_decode($order->biller_detail);
                $shipping_detail = json_decode($order->shipping_detail);

                //get order payments

                $order_payments_markup = ' <table cellpadding="0" cellspacing="0"><tr class="heading"><th align="center"> # </th>
                        <th align="left">Payment Method</th><th align="center">Payment Amount</th><th align="center"> Payment Detail </th>
                      </tr>';

                $order_payments = OrderPayment::where('order_id',$order->id)->get();
                $iteration = 1;
                foreach($order_payments as $payment){
                    if($payment->payment_method == 1){
                        $payment->payment_method = 'Cash';
                    } else {
                        $payment->payment_method = 'Card';
                    }
                    if($payment->payment_type == 1){
                        $payment->payment_type = 'Partial';
                    } else {
                        $payment->payment_type = 'Full';
                    }
                    $payment->method_type = $payment->payment_method.' ( '.$payment->payment_type.' )';
                    $transaction_detail = json_decode($payment->transaction_detail,true);
                    $transaction_detail_markup = '';
                    if(isset($transaction_detail)){
                        foreach($transaction_detail as $key => $value){
                            $transaction_detail_markup .= $key.': '.$value.'<br>';
                        }
                    } else {
                        $transaction_detail_markup = '';
                    }

                    $order_payments_markup .= '<tr class="item" style="text-align: left;">
                            <td width="5%" align="center">'. ($iteration+1) .'</td>
                            <td align="center">'. $payment->method_type.'</td>
                            <td align="center">'. number_format($payment->payment_received,2) .'</td>
                            <td align="center">'. $transaction_detail_markup .'</td>
                        </tr>';
                    $iteration++;
                }


                $email_body = str_replace('{logo}',$logo,$email_body);
                $email_body = str_replace('{store_name}','<b>'. $order->store->name .'</b>',$email_body);
                $email_body = str_replace('{customer_info}',$customer_info,$email_body);

                $email_body = str_replace('{order_number}',$order->reference,$email_body);
                $email_body = str_replace('{order_date}',date('d-m-Y h:i a', strtotime($order->created_at)),$email_body);
                $email_body = str_replace('{order_total}',$currency_symbol . number_format($order->order_total,2) ,$email_body);

                $email_body = str_replace('{payment_method}',$order_payments_markup ,$email_body);
                $email_body = str_replace('{order_status}',($order->payment_status==0?'Pending':'Paid') ,$email_body);
                $email_body = str_replace('{order_type}',($order->order_id==0?'Sales':'Sales Return') ,$email_body);
                $email_body = str_replace('{order_tip}',($order->tip==null?0:$order->tip) ,$email_body);
                $email_body = str_replace('{biller_name}', $biller_detail->name ,$email_body);
                $email_body = str_replace('{biller_email}', $biller_detail->email ,$email_body);
                $email_body = str_replace('{order_note}',($order->order_note?'<b><u>Order Note</u></b><p>'.$order->order_note.'</p>':'') ,$email_body);

                $email_body = str_replace('{order_products}',$order_products,$email_body);

                $email_body = str_replace('{order_sub_total}', $currency_symbol . number_format($order->sub_total,2) ,$email_body);
                $email_body = str_replace('{order_shipping_cost}', $currency_symbol . number_format(@$shipping_detail->cost,2) ,$email_body);
                $email_body = str_replace('{order_service_fee}', $currency_symbol . number_format($order->service_fee,2) ,$email_body);
                $email_body = str_replace('{order_discount}', $currency_symbol . number_format($order->discount,2) ,$email_body);
                $email_body = str_replace('{order_tax}', $currency_symbol . number_format($order->order_tax,2) ,$email_body);
                $email_body = str_replace('{order_grand_total}', $currency_symbol . number_format($order->order_total,2) ,$email_body);

                $body = $email_body;

                Email_template::sendEmail($email_to,$email_data,$body,$order->store_id);
            }

            if(companySettingValue('sales_notifications')==1){

                $email_data = Email_template::where('template_key','sale')->where('company_id',0)->first();

                $store = Store::find($order->store_id);
                $subject = $store->name.' '.'Order # '.$order->reference;
                $email_data->subject = $subject;
                $email_data->from = 'Order Confirmation';
                $email_data->name = 'Order Confirmation';

                $customer = Customer::find($order->customer);

                if($customer){

                    $email_to = companySettingValue('email');
                    $email_body = $email_data->content;

                    $store = Store::find($order->store_id);
                    if($store){
                        $logo = '<img src="'. asset("stores/thumbs/".$store->image) .'" style="width:100%; max-width:300px;">';
                    } else {
                        $logo = '<img src="'. asset("images/logo.png") .'" style="width:100%; max-width:300px;">';
                    }
                    $customer_info = '<p>'. $customer->first_name.' '.$customer->last_name .'</p>';
                    if(!empty($customer->address))
                        $customer_info .= '<p>Address: '. $customer->address.' '.$customer->city. ' ' .$customer->state .'</p>';
                    if(!empty($customer->mobile))
                        $customer_info .= '<p>Phone: '. $customer->mobile .' </p>';

                    $customer_info .= '<p>Email : '. $customer->email .' </p>';
                    $currency_symbol = $order->store->currency->symbol;

                    $order_products = ' <table cellpadding="0" cellspacing="0"><tr class="heading"><th align="center"> # </th>
                            <th align="left"> Item Description </th><th align="center"> Unit Cost </th><th align="center"> Quantity </th>
                            <th align="center">Discount</th><th align="center">Total</th></tr>';

                    $item_array = json_decode($order->order_items);

                    foreach($item_array as $key => $single_item){
                        $unit_price = $single_item->unit_price;

                        $item_modifiers = "";
                        if(isset($single_item->item_modifiers)){
                            $item_modifiers = $single_item->item_modifiers;
                            $modifiers = json_decode($item_modifiers);
                            foreach($modifiers as $modifier){
                                $unit_price = $unit_price+$modifier->price;
                            }
                        }

                        $item_sub_total = $unit_price * $single_item->quantity;
                        $item_discount = $single_item->item_discount;
                        $item_sub_total = $item_sub_total - $item_discount;
                        $item_combos = "";

                        if(isset($single_item->item_combos))
                            $item_combos = $single_item->item_combos;

                        $order_products .= '<tr class="item" style="text-align: left;">
                                <td width="5%" align="center">'. ($key+1) .'</td>
                                <td width="50%" style="text-align: left;">'.$single_item->item_name.'<br/> <span style="color: #aeaeb1;font-style: italic;">'. getProductTagline($single_item->item_id,$item_modifiers,$item_combos) .'</span></td>
                                <td align="center">'. $currency_symbol . number_format($unit_price,2).'</td>
                                <td align="center">'. $single_item->quantity .'</td>
                                <td align="center">'. $currency_symbol . number_format($item_discount,2) .'</td>
                                <td align="center">'. $currency_symbol . number_format($item_sub_total,2) .'</td>
                            </tr>';
                    }

                    $order_products .= '</table>';

                    $biller_detail = json_decode($order->biller_detail);
                    $shipping_detail = json_decode($order->shipping_detail);

                    //get order_payments

                    $email_body = str_replace('{logo}',$logo,$email_body);
                    $email_body = str_replace('{store_name}','<b>'. $order->store->name .'</b>',$email_body);
                    $email_body = str_replace('{customer_info}',$customer_info,$email_body);

                    $email_body = str_replace('{order_number}',$order->reference,$email_body);
                    $email_body = str_replace('{order_date}',date('d-m-Y h:i a', strtotime($order->created_at)),$email_body);
                    $email_body = str_replace('{order_total}',$currency_symbol . number_format($order->order_total,2) ,$email_body);



                    $email_body = str_replace('{payment_method}',($order->payment_method==1?'Cash':'Credit') ,$email_body);
                    $email_body = str_replace('{order_status}',($order->payment_status==0?'Pending':'Paid') ,$email_body);
                    $email_body = str_replace('{order_type}',($order->order_id==0?'Sales':'Sales Return') ,$email_body);
                    $email_body = str_replace('{order_tip}',($order->tip==null?0:$order->tip) ,$email_body);
                    $email_body = str_replace('{biller_name}', $biller_detail->name ,$email_body);
                    $email_body = str_replace('{biller_email}', $biller_detail->email ,$email_body);
                    $email_body = str_replace('{order_note}',($order->order_note?'<b><u>Order Note</u></b><p>'.$order->order_note.'</p>':'') ,$email_body);

                    $email_body = str_replace('{order_products}',$order_products,$email_body);

                    $email_body = str_replace('{order_sub_total}', $currency_symbol . number_format($order->sub_total,2) ,$email_body);
                    $email_body = str_replace('{order_shipping_cost}', $currency_symbol . number_format(@$shipping_detail->cost,2) ,$email_body);
                    $email_body = str_replace('{order_service_fee}', $currency_symbol . number_format($order->service_fee,2) ,$email_body);
                    $email_body = str_replace('{order_discount}', $currency_symbol . number_format($order->discount,2) ,$email_body);
                    $email_body = str_replace('{order_tax}', $currency_symbol . number_format($order->order_tax,2) ,$email_body);
                    $email_body = str_replace('{order_grand_total}', $currency_symbol . number_format($order->order_total,2) ,$email_body);

                    $body = $email_body;

                    Email_template::sendEmail($email_to,$email_data,$body,$order->store_id);
                }
            }



        }
    }

}

if (! function_exists('getCardBrand')) {
    function getCardBrand($pan, $include_sub_types = false)
    {
        //maximum length is not fixed now, there are growing number of CCs has more numbers in length, limiting can give false negatives atm

        //these regexps accept not whole cc numbers too
        //visa
        $visa_regex = "/^4[0-9]{0,}$/";
        $vpreca_regex = "/^428485[0-9]{0,}$/";
        $postepay_regex = "/^(402360|402361|403035|417631|529948){0,}$/";
        $cartasi_regex = "/^(432917|432930|453998)[0-9]{0,}$/";
        $entropay_regex = "/^(406742|410162|431380|459061|533844|522093)[0-9]{0,}$/";
        $o2money_regex = "/^(422793|475743)[0-9]{0,}$/";

        // MasterCard
        $mastercard_regex = "/^(5[1-5]|222[1-9]|22[3-9]|2[3-6]|27[01]|2720)[0-9]{0,}$/";
        $maestro_regex = "/^(5[06789]|6)[0-9]{0,}$/";
        $kukuruza_regex = "/^525477[0-9]{0,}$/";
        $yunacard_regex = "/^541275[0-9]{0,}$/";

        // American Express
        $amex_regex = "/^3[47][0-9]{0,}$/";

        // Diners Club
        $diners_regex = "/^3(?:0[0-59]{1}|[689])[0-9]{0,}$/";

        //Discover
        $discover_regex = "/^(6011|65|64[4-9]|62212[6-9]|6221[3-9]|622[2-8]|6229[01]|62292[0-5])[0-9]{0,}$/";

        //JCB
        $jcb_regex = "/^(?:2131|1800|35)[0-9]{0,}$/";

        //ordering matter in detection, otherwise can give false results in rare cases
        if (preg_match($jcb_regex, $pan)) {
            return "jcb";
        }

        if (preg_match($amex_regex, $pan)) {
            return "amex";
        }

        if (preg_match($diners_regex, $pan)) {
            return "diners_club";
        }

        //sub visa/mastercard cards
        if ($include_sub_types) {
            if (preg_match($vpreca_regex, $pan)) {
                return "v-preca";
            }
            if (preg_match($postepay_regex, $pan)) {
                return "postepay";
            }
            if (preg_match($cartasi_regex, $pan)) {
                return "cartasi";
            }
            if (preg_match($entropay_regex, $pan)) {
                return "entropay";
            }
            if (preg_match($o2money_regex, $pan)) {
                return "o2money";
            }
            if (preg_match($kukuruza_regex, $pan)) {
                return "kukuruza";
            }
            if (preg_match($yunacard_regex, $pan)) {
                return "yunacard";
            }
        }

        if (preg_match($visa_regex, $pan)) {
            return "Visa";
        }

        if (preg_match($mastercard_regex, $pan)) {
            return "Master Card";
        }

        if (preg_match($discover_regex, $pan)) {
            return "discover";
        }

        if (preg_match($maestro_regex, $pan)) {
            if ($pan[0] == '5') {//started 5 must be mastercard
                return "Master Card";
            }
            return "Mastero"; //maestro is all 60-69 which is not something else, thats why this condition in the end

        }

        return "Other"; //unknown for this system
    }

}

if (! function_exists('sendAndroidNotification')) {
    function sendAndroidNotification($body,$device)
    {
        $apiKey = "AAAA3bfV0fI:APA91bGAVl2uzvi7qOC-Dh25J6nWE1MgdxW-QMvfU8Cz3o1DcgUcpRYP2KLMq9SLookM2l1SoBFKmZOBt1UV-BoX3DSQUXnxylcNsKZbb-RibkUp5DkDzd23uwf0d-G_KmA__RtuWeey";

        $device_token = $device->device_token;

        $data = array(
            'to' => $device_token,
            'data' => $body,
        );

        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {

        }


        // Close connection
        curl_close($ch);
        //Log::info(['success' => $result]);

        //$notification->is_send = 1;
        //$notification->save();

    }

}

if (! function_exists('sendIOSNotification')) {
    function sendIOSNotification($body,$device)
    {

        $device_token = $device->device_token;

        $certificate = public_path().'/ios_certificates/development_v2.pem';
        $passphrase = '1';
        $apple_url = 'ssl://gateway.sandbox.push.apple.com:2195';

        if($device->app_mode == 1){
            $certificate = public_path().'/ios_certificates/distribution_v2.pem';
            $passphrase = '1';
            $apple_url = 'ssl://gateway.push.apple.com:2195';
        }

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $certificate);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        $fp = stream_socket_client($apple_url, $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if ($fp){

            $payload = json_encode($body);
            $msg = chr(0) . pack('n', 32) . pack('H*', $device_token) . pack('n', strlen($payload)) . $payload;
            $result = fwrite($fp, $msg, strlen($msg));

//            if ($result){
//
////                $notification->is_send = 1;
////                $notification->save();
//
//            }else{
//
//            }
            fclose($fp);
        }


    }

}

if (! function_exists('getTimeZones')) {
    function getTimeZones()
    {

        static $regions = array(
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
        );

        $timezones = array();
        foreach( $regions as $region )
        {
            $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
        }

        $timezone_offsets = array();
        foreach( $timezones as $timezone )
        {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);

        }

        // sort timezone by offset
        asort($timezone_offsets);

        $timezone_list = array();
        $custom_array = [];
        foreach( $timezone_offsets as $timezone => $offset )
        {
            $temp = [];
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate( 'H:i', abs($offset) );
            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
            $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
            $temp['name'] = "(${pretty_offset}) $timezone";
            //$temp['value'] = $pretty_offset;
            $temp['value'] = "(${pretty_offset}) $timezone".','.$offset_prefix.''.$offset_formatted;
            $custom_array[] = $temp;


        }
        return $custom_array;

    }


}

if (! function_exists('randomString')) {
    function randomString($length = 16)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;


    }


}