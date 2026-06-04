<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisWeight extends Model
{
    use HasFactory;

    protected $fillable = ['criteria', 'weight'];
}
