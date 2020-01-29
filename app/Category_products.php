<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category_products extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'category_products';
    
    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';    
    
    /**
     * belongs To relation Category
     */
    public function category()
    {
    	return $this->belongsTo(Categories::class);
    }
    
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['category_id', 'product_id'];

}
