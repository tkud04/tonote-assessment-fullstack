<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class SalaryDetails extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'salary',
        'benefits'
    ];
}