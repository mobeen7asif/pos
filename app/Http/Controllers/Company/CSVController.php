<?php

namespace App\Http\Controllers\Company;

use App\Categories;
use App\Category_products;
use App\CSV;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Product;
use App\Product_attribute;
use App\Product_images;
use App\Product_modifier;
use App\Product_variant;
use App\Store_products;
use App\StoreCustomer;
use App\Supplier;
use App\Tax_rates;
use App\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;


class CSVController extends Controller{

    public function addCsvView() {
        $pending_csv_file = CSV::where(['csv_type' => 'customers', 'complete_status' => 0])->count();
        $csv_file = CSV::where(['csv_type' => 'customers', 'complete_status' => 0])->first();
        if($csv_file){
            $stats = 'Total customers :' .$csv_file->total_rows."<br />";
            $stats .= 'Inserted customers : '.$csv_file->inserted_rows."<br />";
            $stats .= 'Failed customers : '.$csv_file->failed_records."<br />";
        } else {
            $stats = '';
        }
        return view('company.csv.add_csv',['pending_csv_file' => $pending_csv_file,'stats' => $stats]);
    }

    public function uploadCsvFile(Request $request){
        $this->validate(request(), [
            'store_id' => 'required'
        ]);
        $this->validate($request, [
            'csv_file' => ['required' , function($attribute, $value, $fail) {
                $file_type = $value->getClientOriginalExtension();
                if ($file_type != 'csv') {
                    $fail('Upload csv file');
                }
            },],
        ]);
        $store_id = $request->input('store_id');
        $csv_file = $request->file('csv_file');
        $file_name = $this->saveFile($csv_file,'uploads/csv','csv','customers_'.str_random(10));
        $customers = $this->csvToArray(public_path('uploads/csv/'.$file_name));
        if(count($customers) > 0) {
            $csv_record = CSV::create(['store_id' => $store_id,'file_name' => $file_name,
                'csv_type' => 'customers','customers_data' => json_encode($customers),'total_rows' => count($customers)
            ]);
            if($csv_record) {
                return redirect()->back()->with('success','File uploaded');
            } else {
                return redirect()->back()->with('error','File not uploaded');
            }
        } else {
            return redirect()->back()->with('error','No customers found in file');
        }
    }

    public function insertCustomersData(Request $request){
        $pending_csv_file = CSV::where(['csv_type' => 'customers', 'complete_status' => 0])->first();
        $customers = json_decode($pending_csv_file->customers_data,true);
        $size = $pending_csv_file->offset + 1;
        for($i = $pending_csv_file->offset; $i <= $size; $i++) {
            if ($i < $pending_csv_file->total_rows) {
                $check = Customer::where('email', $customers[$i]['email*'])->first();
                if(!$check) {
                    $first_name_status = false;
                    $last_name_status = false;
                    $email_status = false;
                    if(isset($customers[$i]['first_name*']) && $customers[$i]['first_name*'] != ''){
                        $first_name_status = true;
                    }
                    if(isset($customers[$i]['last_name*']) && $customers[$i]['last_name*'] != ''){
                        $last_name_status = true;
                    }
                    if(isset($customers[$i]['email*']) && $customers[$i]['email*'] != ''){
                        $email_status = true;
                    }
                    if($first_name_status && $last_name_status && $email_status) {
                        $new_customer = new Customer();
                        $new_customer->id = $this->RandomString(15);
                        $new_customer->first_name = $customers[$i]['first_name*'];
                        $new_customer->last_name = $customers[$i]['last_name*'];
                        $new_customer->email = $customers[$i]['email*'];
                        $new_customer->mobile = $customers[$i]['mobile'];
                        $new_customer->company_name = $customers[$i]['company_name'];
                        $new_customer->address = $customers[$i]['address'];
                        $new_customer->state = $customers[$i]['state'];
                        $new_customer->city = $customers[$i]['city'];
                        $new_customer->zip_code = $customers[$i]['zip_code'];
                        $new_customer->note = $customers[$i]['note'];
                        $new_customer->country_id = $customers[$i]['country_code'];
                        $new_customer->ref_code = $this->RandomString(15);
                        if(isset($customers[$i]['dob']) && $customers[$i]['dob'] != '') {
                            $dob = explode('/',$customers[$i]['dob']);
                            if(count($dob) > 0){
                                $new_customer->dob = $dob[2].'-'.$dob[0].'-'.$dob[1];
                            }
                        }
                        if (isset($customers[$i]['password']) && $customers[$i]['password'] != '') {
                            $new_customer->password = bcrypt($customers[$i]['password']);
                        }
                        if (isset($customers[$i]['profile_image'])) {
                            $path = $customers[$i]['profile_image'];
                            $fileName =  str_random(10).'.png';
                            try {
                                Image::make($path)->save(public_path('uploads/customers/' . $fileName));
                                //save thumbnail
                                $img = Image::make(asset('uploads/customers/' . $fileName));
                                $img->resize(100, 100, function ($constraint) {
                                    $constraint->aspectRatio();
                                })->save('uploads/customers/thumbs/' . $fileName);
                            } catch (\Exception $e) {

                            }

                            $new_customer->profile_image = $fileName;
                        }
                        $new_customer->save();
                        if ($new_customer) {
                            $store_customer = new StoreCustomer();
                            $store_customer->customer_id = $new_customer->id;
                            $store_customer->store_id = $pending_csv_file->store_id;
                            $store_customer->company_id = Auth::user()->id;
                            $store_customer->save();
                            updateSyncData('customer',$new_customer->id);

                            $pending_csv_file->increment('inserted_rows');
                            $pending_csv_file->increment('offset');
                        }
                        else {
                            $pending_csv_file->increment('failed_records');
                            $pending_csv_file->increment('offset');
                        }
                    }
                    else {
                        $pending_csv_file->increment('failed_records');
                        $pending_csv_file->increment('offset');
                    }
                }
                else {
                    $pending_csv_file->increment('failed_records');
                    $pending_csv_file->increment('offset');
                }
                if ($pending_csv_file->total_rows == $pending_csv_file->inserted_rows + $pending_csv_file->failed_records) {
                    $pending_csv_file->complete_status = 1;
                    $pending_csv_file->save();
                }
            }
        }
        //send status
        $stats = 'Total customers :' .$pending_csv_file->total_rows."<br />";
        $stats .= 'Inserted customers : '.$pending_csv_file->inserted_rows."<br />";
        $stats .= 'Failed customers : '.$pending_csv_file->failed_records."<br />";
        return response()->json(['complete_status' => $pending_csv_file->complete_status,'stats' => $stats]);
    }

    //csv methods
    public function addProductsCsvView(){
        $pending_csv_file = CSV::where(['csv_type' => 'products', 'complete_status' => 0])->count();
        $csv_file = CSV::where(['csv_type' => 'products', 'complete_status' => 0])->first();
        if($csv_file) {
            $stats = 'Total products :' .$csv_file->total_rows."<br />";
            $stats .= 'Inserted products : '.$csv_file->inserted_rows."<br />";
            $stats .= 'Failed products : '.$csv_file->failed_records."<br />";
        } else {
            $stats = '';
        }
        return view('company.csv.add_products_csv',['pending_csv_file' => $pending_csv_file,'stats' => $stats]);
    }

    public function uploadProductsCsvFile(Request $request){
        $this->validate(request(), [
            'store_id' => 'required'
        ]);
        $this->validate($request, [
            'csv_file' => ['required' , function($attribute, $value, $fail) {
                $file_type = $value->getClientOriginalExtension();
                if ($file_type != 'csv') {
                    $fail('Upload csv file');
                }
            },],
        ]);
        $store_id = $request->input('store_id');
        $csv_file = $request->file('csv_file');
        $file_name = $this->saveFile($csv_file,'uploads/csv','csv','products_'.str_random(10).'.'.$csv_file->getClientOriginalExtension());
        $products = $this->csvToArray(public_path('uploads/csv/'.$file_name));
        if(count($products) > 0) {
            $csv_record = CSV::create(['store_id' => $store_id,'file_name' => $file_name,
                'csv_type' => 'products','customers_data' => json_encode($products),'total_rows' => count($products)
            ]);
            if($csv_record) {
                return redirect()->back()->with('success','File uploaded');
            } else {
                return redirect()->back()->with('error','File not uploaded');
            }
        }
        else {
            return redirect('error','No products found in file');
        }
    }

    public function insertProductsData(Request $request){
        $pending_csv_file = CSV::where(['csv_type' => 'products', 'complete_status' => 0])->first();
        $products = json_decode($pending_csv_file->customers_data,true);
        $size = $pending_csv_file->offset + 1;
        for($i = $pending_csv_file->offset; $i <= $size; $i++) {
            if ($i < $pending_csv_file->total_rows) {
                $product = new Product();
                $code_status = false;
                $name_status = false;
                $type_status = false;
                $sku_status = false;
                if(isset($products[$i]['product_type']) && $products[$i]['product_type'] != '' && $products[$i]['product_type'] == 'standard') {
                    $product->type = 1;
                    $type_status = true;
                }
                else if(isset($products[$i]['product_type']) && $products[$i]['product_type'] != '' && $products[$i]['product_type'] == 'modifier') {
                    $product->type = 3;
                    $type_status = true;
                }
                else {
                    $type_status = false;
                }
                if(isset($products[$i]['bar_code']) && $products[$i]['bar_code'] != '') {
                    $check = Product::where('code',$products[$i]['bar_code'])->first();
                    if(!$check) {
                        $product->code = $products[$i]['bar_code'];
                        $code_status = true;
                    } else {$code_status = false;}
                } else {$code_status = false;}

                if(isset($products[$i]['name']) && $products[$i]['name'] != '') {
                    $product->name = $products[$i]['name'];
                    $name_status = true;
                } else {$name_status = false;}

                if(isset($products[$i]['sku']) && $products[$i]['sku'] != '' && $products[$i]['product_type'] == 'standard') {
                    $check = Product::where('sku',$products[$i]['sku'])->first();
                    if(!$check) {
                        $product->sku = $products[$i]['sku'];
                        $sku_status = true;
                    } else {$sku_status = false;}
                } else {
                    $product->sku = 0;
                    $sku_status = true;
                }
                if(isset($products[$i]['supplier_name']) && $products[$i]['supplier_name'] != '') {
                    $check = Supplier::where('name',$products[$i]['supplier_name'])->first();
                    if($check) {
                        $product->supplier_id = $check->id;
                    } else {
                        $product->supplier_id = 0;
                    }
                } else { $product->supplier_id = 0; }

                if(isset($products[$i]['discount']) && $products[$i]['discount'] != '') {
                    $discount_amount = 0;
                    if (strpos($products[$i]['discount'], '%') !== false) {
                        $product->discount_type = 1;
                        $discount_amount = str_replace('%', '', $products[$i]['discount']);
                    } else {
                        $product->discount_type = 0;
                        $discount_amount = (is_numeric($products[$i]['discount'])) ? $products[$i]['discount'] : 0;
                    }
                    $product->discount = trim($discount_amount);
                } else { $product->discount = 0; $product->discount_type = 1; };

                if(isset($products[$i]['cost']) && $products[$i]['cost'] != '') {
                    $product->cost = $products[$i]['cost'];
                } else { $product->cost = 0; }
                if(isset($products[$i]['price']) && $products[$i]['price'] != '') {
                    $product->price = $products[$i]['price'];
                } else { $product->price = 0; }


                //saving details
                if(isset($products[$i]['details'])) {
                    $product->detail = $products[$i]['details'];
                }
                //saving tax
                if(isset($products[$i]['tax']) && $products[$i]['tax'] != '') {
                    $check = Tax_rates::where('code',$products[$i]['tax'])->first();
                    if($check){
                        $product->tax_rate_id = $check->id;
                    } else {
                        $check = Tax_rates::where('code','NT')->first();
                        $product->tax_rate_id = ($check != null) ? $check->id : 0;
                    }
                } else {
                    $check = Tax_rates::where('code','NT')->first();
                    $product->tax_rate_id = ($check != null) ? $check->id : 0;
                };

                //save product if required conditions are true
                if($code_status && $type_status && $name_status && $sku_status) {

                    $product->company_id = Auth::user()->id;
                    $product->save();
                    if ($product) {
                        if ($products[$i]['product_type'] == 'standard') {
                            //saving images
                            if (isset($products[$i]['images']) && $products[$i]['images'] != '') {
                                $images = explode(',', $products[$i]['images']);
                                foreach ($images as $image) {
                                    $path = $image;
                                    try {
                                        $mime = Image::make($path)->mime();
                                        $fileName = str_random(10) . 'png';
                                        if (isset($mime)) {
                                            $mime = explode('/', $mime);
                                            $fileName = ($mime[1] == 'jpeg' || 'JPEG') ? str_random(10) . '.jpg' : str_random(10) . '.' . $mime[1];
                                        }
                                        Image::make($path)->save(public_path('uploads/products/' . $fileName));
                                        //save thumbnail
                                        $img = Image::make(asset('uploads/products/' . $fileName));
                                        $img->resize(100, 100, function ($constraint) {
                                            $constraint->aspectRatio();
                                        })->save('uploads/products/thumbs/' . $fileName);
                                        $product_image = [];
                                        $product_image['product_id'] = $product->id;
                                        $product_image['name'] = $fileName;
                                        $product_image['default'] = 0;
                                        $product_image['is_active'] = 1;
                                        Product_images::create($product_image);
                                    } catch (\Exception $e) {

                                    }
                                }
                            }
                        }

                        //saving category
                        if (isset($products[$i]['category']) && $products[$i]['category'] != '') {
                            $store_id = $pending_csv_file->store_id;
                            $category = Categories::where('category_name', $products[$i]['category'])->first();
                            if ($category) {
                                if ($products[$i]['product_type'] == 'modifier') {
                                    $input['store_category_ids'] = 'store-' . $store_id;
                                } else {
                                    $input['store_category_ids'] = 'store-' . $store_id . ',category-' . $category->id;
                                }
                                $quantity = $products[$i]['quantity'];
                                $input['store_quantity_store-' . $store_id] = (isset($quantity) && is_numeric($quantity)) ? $quantity : 0;
                                $this->updateStore($product->id, $input);
                            }
                            else {
                                $quantity = $products[$i]['quantity'];
                                $input['store_category_ids'] = 'store-' . $pending_csv_file->store_id;
                                $input['store_quantity_store-' . $pending_csv_file->store_id] = (isset($quantity) && is_numeric($quantity)) ? $quantity : 0;
                                $this->updateStore($product->id, $input);
                            }
                        }
                        else {
                            $quantity = $products[$i]['quantity'];
                            $input['store_category_ids'] = 'store-' . $pending_csv_file->store_id;
                            $input['store_quantity_store-' . $pending_csv_file->store_id] = (isset($quantity) && is_numeric($quantity)) ? $quantity : 0;
                            $this->updateStore($product->id, $input);
                        }

                        if ($products[$i]['product_type'] == 'standard') {

                            //check for both
                            if ((isset($products[$i]['varient']) && $products[$i]['varient'] != '') && (isset($products[$i]['modifier']) && $products[$i]['modifier'] != '')) {
                            }
                            else {
                                //saving variants
                                if (isset($products[$i]['varient']) && $products[$i]['varient'] != '') {
                                    $variants = explode(',', $products[$i]['varient']);
                                    if (count($variants) > 0) {
                                        foreach ($variants as $variant) {
                                            $attr_variant = explode(':', $variant);
                                            if (count($attr_variant) > 0) {
                                                $attr = $attr_variant[0];
                                                $var = $attr_variant[1];
                                                //check if attribute exists
                                                $db_attribute = Variant::where(['company_id' => Auth::user()->id, 'name' => $attr])->first();
                                                if (!$db_attribute) {
                                                    $db_attribute = new Variant();
                                                    $db_attribute->company_id = Auth::user()->id;
                                                    $db_attribute->name = $attr;
                                                    $db_attribute->save();
                                                }
                                                //saving product attribute
                                                $attribute_data['product_id'] = $product->id;
                                                $attribute_data['attribute_id'] = $db_attribute->id;
                                                $saved_product_attr = $this->createProductAttribute($attribute_data);
                                                //saving product variant
                                                $attr_variant_string = 'total_attributes=1&attribute-id-1=' . $saved_product_attr->id . '&attribute-1=' . $var . '&is_main_price=1&cost=&price=';
                                                $this->createProductVariant($attr_variant_string, $product->id);
                                            }
                                        }
                                        $check_variant = Product::where('product_id',$product->id)->first();
                                        $product->is_variants = ($check_variant == null) ? 0 : 1;
                                        $product->save();
                                    }
                                }
                                else {
                                    if (isset($products[$i]['modifier']) && $products[$i]['modifier'] != '') {
                                        $product_modifiers = explode(',', $products[$i]['modifier']);
                                        foreach ($product_modifiers as $modifier) {
                                            $db_modifier = Product::where(['name' => $modifier, 'type' => 3, 'company_id' => Auth::user()->id])->first();
                                            if ($db_modifier) {
                                                $check = Product_modifier::where(['product_id' => $product->id,'modifier_id' => $db_modifier->id])->first();
                                                if($check == null) {
                                                    $product_modifier_data = [];
                                                    $product_modifier_data['product_id'] = $product->id;
                                                    $product_modifier_data['modifier_id'] = $db_modifier->id;
                                                    Product_modifier::create($product_modifier_data);
                                                }
                                                $check_modifier_count = Product_modifier::where(['product_id' => $product->id,'modifier_id' => $db_modifier->id])->count();
                                                $product->is_modifier = ($check_modifier_count > 0) ? 1 : 0;
                                            }
                                        }
                                    }
                                    $product->save();
                                }
                            }

                        }
                    }

                    $pending_csv_file->increment('inserted_rows');
                    $pending_csv_file->increment('offset');
                }
                else {
                    $pending_csv_file->increment('failed_records');
                    $pending_csv_file->increment('offset');
                }
                if ($pending_csv_file->total_rows == $pending_csv_file->inserted_rows + $pending_csv_file->failed_records) {
                    $pending_csv_file->complete_status = 1;
                    $pending_csv_file->save();
                }
            }
        }
        //send status


        $stats = 'Total products :' .$pending_csv_file->total_rows."<br />";
        $stats .= 'Inserted products : '.$pending_csv_file->inserted_rows."<br />";
        $stats .= 'Failed products : '.$pending_csv_file->failed_records."<br />";

        return response()->json(['complete_status' => $pending_csv_file->complete_status,'stats' => $stats]);
    }

    //helper methods

    public function createProductAttribute($attribute_data)
    {
        if($attribute_data['attribute_id'] == 0){
            $product_variant['product_id'] =  $attribute_data['product_id'];
            $product_variant['variant_id'] =  $attribute_data['attribute_id'];

            $product_variants = Product_attribute::firstOrNew($product_variant);
            $product_variants->save();
        }else if($attribute_data['attribute_id'] > 0){
            $product_variant['product_id'] =  $attribute_data['product_id'];
            $product_variant['variant_id'] =  $attribute_data['attribute_id'];

            $product_variants = Product_attribute::updateOrCreate($product_variant);
            $product_variants->save();
        }
        // sync products
        updateSyncData('product',$attribute_data['product_id']);
        return $product_variants;

    }

    public function createProductVariant($attribute_data,$product_id)
    {
        $product = Product::with('store_products')->find($product_id);
        $post_data = array();
        parse_str($attribute_data, $post_data);
        $variant_name = [];
        for($i=1; $i<=(int)$post_data['total_attributes']; $i++){
            $variant_name[] = $post_data['attribute-'.$i];
        }

        $requestData['company_id'] = Auth::id();
        $requestData['product_id'] = $product->id;
        $requestData['name'] = $product->name .' - '. implode(', ', $variant_name);
        $requestData['cost'] = empty($post_data['cost']) ? 0 : $post_data['cost'];
        $requestData['price'] = empty($post_data['price']) ? 0 : $post_data['price'];
        $requestData['code'] = 0;
        $requestData['sku'] = 0;
        $requestData['discount_type'] = 0;
        $requestData['discount'] = 0;
        $requestData['supplier_id'] = 0;
        $requestData['tax_rate_id'] = companySettingValue('tax_id');
        if(isset($post_data['is_main_price'])){
            $requestData['is_main_price'] = $post_data['is_main_price'];
        } else {
            $requestData['is_main_price'] = 0;
        }
        $variant_product = Product::create($requestData);

        if($variant_product){
            for($i=1; $i<=(int)$post_data['total_attributes']; $i++){
                $variant_data['product_attribute_id'] = $post_data['attribute-id-'.$i];
                $variant_data['variant_product_id'] = $variant_product->id;

                $product_variant = Product_variant::firstOrNew($variant_data);
                $product_variant->name= $post_data['attribute-'.$i];
                $product_variant->save();
            }

            //set first record as default
            $this->setFirstProductAsDefault($variant_product->id);

            $product->store_products->map(function($store_product) use ($variant_product) {

                $variant_store_product = [];
                $variant_store_product['store_id'] =  $store_product->store_id;
                $variant_store_product['product_id'] =  $variant_product->id;
                $variant_store_product['quantity'] =  0;

                Store_products::create($variant_store_product);

                return $store_product;
            });

            // sync products
            updateSyncData('product',$variant_product->product_id);
            //updateSyncData('product',$variant_product->id);
        }


    }

    public function setProductModifier($product_modifier_data)
    {
        $product_modifier['product_id'] = $product_modifier_data['product_id'];
        $product_modifier['modifier_id'] = $product_modifier_data['modifier_id'];

        if($product_modifier_data['value']==1){
            $product_modifiers = Product_modifier::firstOrNew($product_modifier);
            $product_modifiers->save();
        }else{
            Product_modifier::where($product_modifier)->delete();
        }

        // sync products
        updateSyncData('product',$product_modifier_data['product_id']);

    }

    public function updateStore($id,$requestData)
    {
        $product = Product::findOrFail($id);

        //$requestData = $request->all();


        $product->update($requestData);

        // remove store products and category products and product stock and product tags
        Category_products::where('product_id',$product->id)->delete();

        $store_category_ids = explode(',', $requestData['store_category_ids']);

        $old_store_ids = Store_products::select('store_id','quantity')->where('product_id',$product->id)->get();
        $new_store_ids = [];

        foreach($store_category_ids as $store_category_id){
            $store_category = explode('-', $store_category_id);

            if($store_category[0] == "store"){

                $new_quantity = empty($requestData['store_quantity_'.$store_category_id]) ? 0 : $requestData['store_quantity_'.$store_category_id];
                $store_id = $store_category[1];


                // Check store product exist or not
                $store_product_data = Store_products::where('product_id',$product->id)->where('store_id',$store_id)->first();

                //get low stock value for store
                //$low_stock = empty($requestData['low_stock_'.$store_category_id]) ? 0 : $requestData['low_stock_'.$store_category_id];
                //get lowstock status
                //$low_stock_status = $requestData['low_status_'.$store_category_id];

                if($store_product_data){

                    //save low stock
                    //$store_product_data->low_stock = $low_stock;
                    //$store_product_data->low_stock_status = $low_stock_status;
                    $store_product_data->save();

                    if($new_quantity > $store_product_data->quantity){
                        // stock add
                        $add_quantity = $new_quantity - $store_product_data->quantity;
                        updateProductStockByData($product->id, $store_id, $add_quantity, 1, 2, 0, 0, 'Edit Product');
                    }elseif($new_quantity < $store_product_data->quantity){
                        // stock remove
                        $remove_quantity = $store_product_data->quantity - $new_quantity;
                        updateProductStockByData($product->id, $store_id, $remove_quantity, 2, 2, 0, 0, 'Edit Product');
                    }
                }else{
                    // save new store product
                    $store_product['product_id'] = $product->id;
                    $store_product['store_id'] = $store_id;
                    $store_product['quantity'] = $new_quantity;
                    //$store_product['low_stock'] = $low_stock;
                    //$store_product['low_stock_status'] = $low_stock_status;
                    $store_products = Store_products::create($store_product);

                    updateProductStockByData($product->id, $store_id, $new_quantity, 1, 1, 0, 0, 'Add Product');
                }

                $new_store_product['store_id'] = $store_id;
                $new_store_product['quantity'] = empty($requestData['store_quantity_'.$store_category_id]) ? 0 : $requestData['store_quantity_'.$store_category_id];

                array_push($new_store_ids, $new_store_product);


            }

            if($store_category[0] == "category" || $store_category[0] == "subcategory"){
                $category_id =  $store_category[1];

                if($store_category[0] == "category"){
                    // save store product
                    $store_id = Categories::find($category_id)->store_id;

                    $store_product['product_id'] = $product->id;
                    $store_product['store_id'] = $store_id;

                    $store_products = Store_products::firstOrNew($store_product);
                    $store_products->save();
                }

                if($store_category[0] == "subcategory"){

                    $parent_id = Categories::find($category_id)->parent_id;

                    $store_id = Categories::find($parent_id)->store_id;

                    // save store product
                    $store_product['product_id'] = $product->id;
                    $store_product['store_id'] = $store_id;

                    $store_products = Store_products::firstOrNew($store_product);
                    $store_products->save();

                    // save category products
                    $category_product['product_id'] = $product->id;
                    $category_product['category_id'] = $parent_id;

                    $category_products = Category_products::firstOrNew($category_product);
                    $category_products->save();
                }

                // save category products
                $category_product['product_id'] = $product->id;
                $category_product['category_id'] = $category_id;

                $category_products = Category_products::firstOrNew($category_product);
                $category_products->save();
            }
        }

        foreach($old_store_ids as $old_store_id){
            if(find_key_value($new_store_ids,'store_id',$old_store_id['store_id'])){

            }else{
                // Check store product exist or not
                $store_product_data = Store_products::where('product_id',$product->id)->where('store_id',$old_store_id['store_id'])->first();

                if($store_product_data){
                    // stock remove
                    $remove_quantity = $store_product_data->quantity;
                    updateProductStockByData($product->id, $old_store_id['store_id'], $remove_quantity, 2, 2, 0, 0, 'Edit Product');
                }
            }
        }

        // sync products
        updateSyncData('product',$product->id);

    }

    public function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }
        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header) {
                    $header = $row;
                }
                else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    public function saveCsvFile($csv_file){
        $destinationPath = 'uploads/csv'; // upload path
        $extension = $csv_file->getClientOriginalExtension(); // getting image extension
        $fileName = 'customers-'.str_random(10).'.'.$extension; // remaining file
        //create directory if not exists
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        $csv_file->move($destinationPath, $fileName); // uploading file to given path
        return $fileName;
    }

    public function saveFile($input_file,$destinationPath,$file_type = 'image',$fileName){
        if($file_type == 'image'){
            $img = Image::make($input_file->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);
        }
        $input_file->move($destinationPath, $fileName); // uploading file to given path
        return $fileName;
    }

    public function RandomString($length = 16)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function setFirstProductAsDefault($product_id)
    {
        $product = Product::find($product_id);

        $total_default_products = Product::where('product_id',$product->product_id)->where('is_default' , 1)->count();

        if($total_default_products==0){
            $first_product = Product::where('product_id',$product->product_id)->orderBy('id','asc')->first();
            $first_product->is_default = 1;
            $first_product->save();

            // sync products
            updateSyncData('product',$product->product_id);
        }

    }
}
