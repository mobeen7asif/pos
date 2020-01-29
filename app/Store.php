<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Store extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */ 
    protected $table = 'stores';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';
    
    /**
     * has Many relation Company
     */
    public function company()
    {
    	return $this->belongsTo(Company::class, 'company_id');
    }

    public function duty_setting()
    {
        return $this->hasOne(DutySetting::class, 'store_id');
    }
   
    /**
     * has Many relation Currency
     */
    public function currency()
    {
    	return $this->belongsTo(Currency::class, 'currency_id');
    }
    
    /**
     * has Many relation Categories
     */

    public function categories()
    {
    	return $this->hasMany(Categories::class, 'store_id');
    }
    
    /**
     * has Many relation Store_products
     */

    public function store_products()
    {
    	return $this->hasMany(Store_products::class, 'store_id');
    }
    public function meal_types()
    {
        return $this->hasMany(MealType::class, 'store_id');
    }
    public function floors()
    {
        return $this->hasMany(Floor::class, 'store_id');
    }
    public function discountCategories()
    {
        return $this->hasMany('App\DiscountCategory','store_id');
    }

    public function discountProducts()
    {
        return $this->hasMany('App\DiscountProduct','store_id');
    }
    public function discountBogo()
    {
        return $this->hasMany('App\DiscountBogo','store_id');
    }
    
    /**
     * boot
     */
    protected static function boot ()
    {
    	parent::boot();
        
        
//        static::addGlobalScope('company_id', function (Builder $builder) {
//            $builder->where('company_id',  Auth::id());
//        });
        
    	static::deleting(function($store) {
            $store->categories()->delete();
            $store->store_products()->delete();
            $store->meal_types()->delete();
            $store->discountCategories()->delete();
            $store->discountProducts()->delete();
            $store->discountBogo()->delete();
    	});
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id','name', 'tax_id','phone','manager', 'currency_id', 'address', 'image','uid','major','minor','time_zone','background_image','header_content','footer_content','receipt_logo','break_time','set_break_time','number_of_prints','user_name','password','host'];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     *
     */
    protected $hidden = ['created_at','updated_at','deleted_at'];


    //protected $appends = array('thumbnail','fullImage');

    //protected $appends = array('background');
//
//    public function getthumbnailAttribute()
//    {
//        $image_name = 'no_image.png';
//        if($this->attributes['image']){
//            $image_name = 'stores/thumbs/'.$this->attributes['image'];
//        }
//        //return 'hello';
//        return checkImage($image_name);
//
//    }
//    public function getfullImageAttribute()
//    {
//        $image_name = 'no_image.png';
//        if($this->attributes['image']){
//            $image_name = 'stores/'.$this->attributes['image'];
//        }
//        return checkImage($image_name);
//
//    }

//    public function getbackgroundAttribute()
//    {
//        $image_name = 'no_image.png';
//        if($this->attributes['image']){
//            $image_name = 'stores/thumbs/'.$this->attributes['image'];
//        }
//        //return 'hello';
//        return checkImage($image_name);
//
//    }

    use SoftDeletes;
    protected $dates = ['deleted_at'];
	
}
