<?phpnamespace App;use Illuminate\Database\Eloquent\Model;use File;use Illuminate\Database\Eloquent\Builder;class Floor extends Model{    /**     * The database table used by the model.     *     * @var string     */    protected $table = 'floors';    /**    * The database primary key value.    *    * @var string    */    protected $primaryKey = 'id';                   /**     * belongs To relation User     */    /**     * The attributes that should be hidden for arrays.     *     * @var array     */    protected $hidden = [        'created_at', 'updated_at'    ];    /**     * belongs To relation Product     */    public function store()    {        return $this->belongsTo(Store::class,'store_id');    }    public function tables()    {        return $this->hasMany(FloorTable::class,'floor_id');    }    /**     * Attributes that should be mass-assignable.     *     * @var array     */    protected $fillable = ['id','store_id', 'name','company_id','image','created_at', 'updated_at'];    protected $appends = array('thumbnail','fullImage');    public function getthumbnailAttribute()    {        $image_name = 'no_image.png';        if($this->attributes['image']){            $image_name = 'floors/thumbs/'.$this->attributes['image'];        }        //return 'hello';        return checkImage($image_name);    }    public function getfullImageAttribute()    {        $image_name = 'no_image.png';        if($this->attributes['image']){            $image_name = 'floors/'.$this->attributes['image'];        }        return checkImage($image_name);    }}