<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class B20Nhanh_tontucthoi extends Model
{
    protected $fillable = [
        'ItemId','Code', 'K12435', 'K16186', 'K16187', 'K18414', 'K19536', 'K22885', 'K25405', 'K30719', 'K44298', 'K58454', 'K58601', 'K63530', 'K70310', 'K132462', 'K132718', 'K132719', 'K132720', 'K132761', 'K133563'
    ];
    protected $primaryKey ='Id';
    protected $table = "B20Nhanh_tontucthoi";
    public $timestamps = false;
}

