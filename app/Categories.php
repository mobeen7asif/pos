<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';
    
    
    /**
     * has Many relation Subcategories
     */
    public function subcategories()
    {
    	return $this->hasMany(Categories::class, 'parent_id');
    }

    public function discountCategories()
    {
        return $this->hasMany(DiscountCategory::class, 'category_id');
    }
   
    /**
     * belongs To relation Categories
     */
    public function category()
    {
    	return $this->belongsTo(Categories::class, 'parent_id');
    }
    
    /**
     * belongs To relation Store
     */
    public function store()
    {
    	return $this->belongsTo(Store::class, 'store_id');
    }

    public function discountCategory()
    {
        return $this->belongsTo(DiscountCategory::class, 'id','id');
    }

    /**
     * has Many relation Category_products
     */

    public function category_products()
    {
    	return $this->hasMany(Category_products::class, 'category_id');
    }
    
    /**
     * boot
     */
    protected static function boot ()
    {
    	parent::boot();

    	static::deleting(function($category) {
            $category->category_products()->delete();
            $category->discountCategories()->delete();
    	});
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'store_id', 'category_name', 'category_image', 'description', 'is_active'];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $appends = array('thumbnail','fullImage');

    public function getthumbnailAttribute()
    {
        $image_name = 'no_image.png';
        if($this->attributes['category_image']){
            $image_name = 'categories/thumbs/'.$this->attributes['category_image'];
        }
        //return 'hello';
        return checkImage($image_name);

    }
    public function getfullImageAttribute()
    {
        $image_name = 'no_image.png';
        if($this->attributes['category_image']){
            $image_name = 'categories/'.$this->attributes['category_image'];
        }
        return checkImage($image_name);

    }

   protected $hidden = [
       'is_active','created_at','updated_at','deleted_at'
   ];
   
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
}
