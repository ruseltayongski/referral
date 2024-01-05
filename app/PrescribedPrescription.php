<?php
/**
 * Created by PhpStorm.
 * User: DOH-IT Lex
 * Date: 01/05/2024
 * Time: 1:31 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrescribedPrescription extends Model
{
    protected $table = 'prescribed_prescriptions';
    protected $guarded = array();

    public function prescribedActivityId() {
        return $this -> belongsTo(Activity::class, 'prescribed_activity_id', 'id');
    }
    public function Code() {
        return $this -> belongsTo(Activity::class, 'code', 'code');
    }
}