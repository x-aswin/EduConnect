<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MentorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();
        $mentors = $this->filteredMentorsQuery($request, $college->id)->get();

        return view('college.manage-mentor', compact('mentors', 'college'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:pending,active,blocked',
            'qualification' => 'required|string|max:255',
            'expertise' => 'required|string|max:255',
            'bio' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::transaction(function () use ($request, $college) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'mentor',
                'status' => $request->status,
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('mentors/photos', 'public');
            }

            Mentor::create([
                'user_id' => $user->id,
                'college_id' => $college->id,
                'qualification' => $request->qualification,
                'expertise' => $request->expertise,
                'bio' => $request->bio,
                'photo' => $photoPath,
            ]);
        });

        return redirect()->route('college.mentors.index')->with('success', 'Mentor registered successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();
        $mentors = $this->filteredMentorsQuery($request, $college->id)->get();
        $editMentor = Mentor::with(['user', 'college.user'])
            ->where('college_id', $college->id)
            ->findOrFail($id);
        $viewOnly = true;

        return view('college.manage-mentor', compact('mentors', 'college', 'editMentor', 'viewOnly'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();
        $mentors = $this->filteredMentorsQuery($request, $college->id)->get();
        $isDeleteMode = $request->query('mode') === 'delete';

        if (!$isDeleteMode) {
            $editMentor = Mentor::with(['user', 'college.user'])
                ->where('college_id', $college->id)
                ->findOrFail($id);
            return view('college.manage-mentor', compact('mentors', 'college', 'editMentor'));
        }

        $deleteMentor = Mentor::with(['user', 'college.user'])
            ->where('college_id', $college->id)
            ->findOrFail($id);

        return view('college.manage-mentor', compact('mentors', 'college', 'deleteMentor', 'isDeleteMode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();
        $mentor = Mentor::with('user')
            ->where('college_id', $college->id)
            ->findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $mentor->user->id,
            'status' => 'required|in:pending,active,blocked',
            'qualification' => 'required|string|max:255',
            'expertise' => 'required|string|max:255',
            'bio' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $mentor, $college) {
            $mentor->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->status,
            ]);

            if ($request->filled('password')) {
                $mentor->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $updateData = [
                'college_id' => $college->id,
                'qualification' => $request->qualification,
                'expertise' => $request->expertise,
                'bio' => $request->bio,
            ];

            if ($request->hasFile('photo')) {
                if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                    Storage::disk('public')->delete($mentor->photo);
                }
                $updateData['photo'] = $request->file('photo')->store('mentors/photos', 'public');
            }

            $mentor->update($updateData);
        });

        return redirect()->route('college.mentors.index')->with('success', 'Mentor updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();
        $mentor = Mentor::with('user')
            ->where('college_id', $college->id)
            ->findOrFail($id);
        $user = $mentor->user;

        DB::transaction(function () use ($mentor, $user) {
            if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                Storage::disk('public')->delete($mentor->photo);
            }

            $mentor->delete();
            $user->delete();
        });

        return redirect()->route('college.mentors.index')->with('success', 'Mentor and associated account deleted successfully!');
    }

    private function filteredMentorsQuery(Request $request, int $collegeId): Builder
    {
        $query = Mentor::with(['user', 'college.user'])
            ->where('college_id', $collegeId)
            ->whereHas('user');

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('expertise', 'like', "%{$search}%")
                    ->orWhere('qualification', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $status = $request->input('status');
        if (in_array($status, ['active', 'pending', 'blocked'], true)) {
            $query->whereHas('user', function (Builder $builder) use ($status) {
                $builder->where('status', $status);
            });
        }

        $this->applyDateFilter($query, $request);

        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'name_asc') {
            $query->join('users as mentor_users', 'mentors.user_id', '=', 'mentor_users.id')
                ->orderBy('mentor_users.name')
                ->select('mentors.*');
        } elseif ($sort === 'name_desc') {
            $query->join('users as mentor_users', 'mentors.user_id', '=', 'mentor_users.id')
                ->orderByDesc('mentor_users.name')
                ->select('mentors.*');
        } else {
            $query->latest();
        }

        return $query;
    }

    private function applyDateFilter(Builder $query, Request $request): void
    {
        $dateFilter = $request->input('date_filter', 'all');

        if ($dateFilter === 'today') {
            $query->whereDate('created_at', now()->toDateString());
            return;
        }

        if ($dateFilter === 'last_7') {
            $query->whereDate('created_at', '>=', now()->subDays(7)->toDateString());
            return;
        }

        if ($dateFilter === 'last_30') {
            $query->whereDate('created_at', '>=', now()->subDays(30)->toDateString());
            return;
        }

        if ($dateFilter === 'last_90') {
            $query->whereDate('created_at', '>=', now()->subDays(90)->toDateString());
            return;
        }

        if ($dateFilter === 'custom') {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }
    }
}
