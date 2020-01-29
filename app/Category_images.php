<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category_images extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'category_images';
    
    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';
    
    protected $visible = ['id', 'item_thumbnail' , 'item_image','name'];
    
    /**
     * belongs To relation Category
     */
    public function categories()
    {
    	return $this->belongsTo(Categories::class);
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['category_id', 'user_id', 'name', 'is_active'];

}
