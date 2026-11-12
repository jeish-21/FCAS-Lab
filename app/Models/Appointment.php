<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationship para sa Student nga nag-book
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relationship para sa Faculty nga gi-bookan
    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }
}