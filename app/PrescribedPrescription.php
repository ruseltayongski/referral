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
    //protected $guarded = array();

    protected $fillable = [
        'prescribed_activity_id',
        'code',
        'generic_name',
        'dosage',
        'formulation',
        'brandname',
        'frequency',
        'duration',
        'quantity',
        'prescription_v2', // Add these
        'referred_from',    // Add these
        'referred_to',      // Add these
    ];

    public function activity() {
        return $this -> belongsTo(Activity::class, 'prescribed_activity_id', 'id');
    }
    public function activityByCode() {
        return $this -> belongsTo(Activity::class, 'code', 'code');
    }
}