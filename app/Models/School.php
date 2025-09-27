<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $primaryKey = 'school_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['school_id','school_name','school_type','prefecture'];
}
