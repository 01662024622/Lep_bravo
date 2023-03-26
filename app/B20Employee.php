<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class B20Employee extends Model
{
    protected $fillable = [
        'Code', 'Name'
    ];
    protected $primaryKey = 'Id';
    protected $table = "B20Employee";
    public $timestamps = false;
    public static function getEmployee(int $id): int
    {

        $employee = B20Employee::where('Code', (string)$id)->get();
        if (sizeof($employee) > 0) {
            $employee = $employee[0];
            return $employee->Id;
        } else {
            return 0;
        }
    }
}
