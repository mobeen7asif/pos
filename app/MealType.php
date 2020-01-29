<?phpnamespace App;use Illuminate\Database\Eloquent\Model;use File;use Illuminate\Database\Eloquent\Builder;class MealType extends Model{    /**     * The database table used by the model.     *     * @var string     */    protected $table = 'meal_types';    /**    * The database primary key value.    *    * @var string    */    protected $primaryKey = 'id';                   /**     * belongs To relation User     */    protected static function boot() {        parent::boot();        static::addGlobalScope('order', function (Builder $builder) {            $builder->orderBy('sort_id', 'asc');        });    }    /**     * The attributes that should be hidden for arrays.     *     * @var array     */    protected $hidden = [        'created_at', 'updated_at'    ];    /**     * belongs To relation Product     */    public function store()    {        return $this->belongsTo(Store::class,'store_id');    }    /**     * Attributes that should be mass-assignable.     *     * @var array     */    protected $fillable = ['id','store_id', 'sort_id', 'meal_type','company_id','color','ip','printer_type','created_at', 'updated_at'];}