<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KalenderAkademik extends Model
{
    use HasFactory;

    protected $table = 'kalender_akademik';
    
    protected $fillable = [
        'kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'class_type'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date'
    ];

    public function scopeFilterByClass($query, $classType)
    {
        if ($classType && $classType !== 'all') {
            return $query->where('class_type', $classType);
        }
        return $query;
    }

    public function scopeOrderByDate($query, $orderBy, $direction = 'asc')
    {
        if ($orderBy === 'tanggal_mulai') {
            return $query->orderBy('tanggal_mulai', $direction);
        } elseif ($orderBy === 'tanggal_selesai') {
            return $query->orderBy('tanggal_selesai', $direction);
        }
        return $query;
    }
}
