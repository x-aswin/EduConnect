<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'college_id', 'category_id', 'mentor_id', 'title', 'slug', 
    'description', 'course_image', 'course_type', 'price', 
    'firm_duration',
    'is_certified', 'total_seats', 'available_seats', 
    'start_date', 'end_date', 'time_slot', 'venue', 'status'
])]
class Course extends Model
{
    protected static function booted()
    {
        static::creating(function ($course) {
            $college = College::find($course->college_id);
            $acronym = $college?->user?->name ?? time();
            $course->slug = Str::slug($course->title) . '-' .
                            Str::slug($acronym) . '-' .
                            now()->format('Y-m');
            $course->available_seats = $course->total_seats;
        });

        static::updating(function ($course) {
            if ($course->isDirty('title')) {
                $acronym = $course->college?->user?->name ?? time();
                $course->slug = Str::slug($course->title) . '-' . 
                            Str::slug($acronym) . '-' . 
                            now()->format('Y-m');
            }
        });
    }


    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('priority_order', 'asc');;
    }
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
    public function certificateSignatories(): HasMany
    {
        return $this->hasMany(CertificateSignatory::class)->orderBy('display_order');
    }

}
