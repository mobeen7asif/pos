<?phpnamespace App;use Illuminate\Database\Eloquent\Model;class CSV extends Model{    /**     * The database table used by the model.     *     * @var string     */    protected $table = 'csv_files';    /**    * The database primary key value.    *    * @var string    */    protected $primaryKey = 'id';                   /**     * belongs To relation User     */    /**     * The attributes that should be hidden for arrays.     *     * @var array     */    protected $hidden = [        'created_at', 'updated_at'    ];    /**     * Attributes that should be mass-assignable.     *     * @var array     */    protected $fillable = ['id','store_id','file_name', 'total_rows', 'inserted_rows','csv_type','complete_status','offset','customers_data','failed_records','created_at', 'updated_at'];}