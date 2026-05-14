<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use Carbon\Carbon;

class CourseController extends Controller
{
    public function browse(Request $request)
    {
        $query = Course::with(['college.user', 'category'])
            ->where('mentor_id', auth()->user()->mentor?->id);

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    ->orWhere('venue', 'like', '%' . $keyword . '%')
                    ->orWhereHas('college', function ($collegeQuery) use ($keyword) {
                        $collegeQuery->where('institution_name', 'like', '%' . $keyword . '%')
                            ->orWhereHas('user', function ($userQuery) use ($keyword) {
                                $userQuery->where('name', 'like', '%' . $keyword . '%');
                            });
                    })
                    ->orWhereHas('category', function ($categoryQuery) use ($keyword) {
                        $categoryQuery->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if ($request->filled('category')) {
            $categoryName = trim((string) $request->category);
            $query->whereHas('category', function ($categoryQuery) use ($categoryName) {
                $categoryQuery->where('name', $categoryName);
            });
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc')->orderBy('created_at', 'desc');
        } elseif ($sort === 'start_soon') {
            $query->orderBy('start_date', 'asc')->orderBy('created_at', 'desc');
        } else {
            $query->latest();
            $sort = 'newest';
        }

        $courses = $query
            ->paginate(9)
            ->withQueryString()
            ->through(function (Course $course) {
                $gradients = [
                    'linear-gradient(135deg, #d1fae5, #a7f3d0)',
                    'linear-gradient(135deg, #fce7f3, #fbcfe8)',
                    'linear-gradient(135deg, #fef3c7, #fde68a)',
                    'linear-gradient(135deg, #e0e7ff, #a5b4fc)',
                    'linear-gradient(135deg, #e0e7ff, #c7d2fe)',
                    'linear-gradient(135deg, #d1fae5, #6ee7b7)',
                ];

                $icons = [
                    'bi-shield-shaded', 'bi-graph-up-arrow', 'bi-palette2',
                    'bi-bar-chart-line', 'bi-code-slash', 'bi-camera-video',
                ];

                $colors = ['text-success', 'text-danger', 'text-warning', 'text-primary', 'text-info'];

                $visuals = [
                    'image_bg' => $gradients[array_rand($gradients)],
                    'icon' => $icons[array_rand($icons)],
                    'icon_color' => $colors[array_rand($colors)],
                ];

                $imagePath = $course->course_image ?? $course->college?->photo ?? null;
                $imageUrl = $imagePath ? asset('storage/' . ltrim($imagePath, '/')) : null;

                return [
                    'id' => $course->id,
                    'slug' => $course->slug,
                    'title' => $course->title,
                    'image' => $imageUrl,
                    'category' => $course->category?->name ?? null,
                    'college' => $course->college?->institution_name ?? $course->college?->user?->name ?? 'Unknown College',
                    'venue' => $course->venue ?? 'TBA',
                    'start_date' => $course->start_date ? Carbon::parse($course->start_date)->format('M d, Y') : 'TBA',
                    'price' => ((float) $course->price <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0),
                    'seats_total' => (int) ($course->total_seats ?? 0),
                    'seats_available' => (int) ($course->available_seats ?? 0),
                    'type' => $course->course_type,
                    'status' => $course->status ?? 'active',
                    ...$visuals,
                ];
            });

        $categories = Category::query()
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->values();

        return view('mentor.browse-course', compact('courses', 'categories', 'sort'));
    }
}
