<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OCEvent extends Model
{
    // ★ これを追加
    protected $table = 'oc_events';

    protected $primaryKey = 'ocev_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['ocev_id','dept_id','date','start_time','end_time','place','reservation_url'];
    protected $casts = ['date' => 'date'];
}
