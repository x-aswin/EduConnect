<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Firm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FirmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $status = $request->get('status', 'all');

        $firms = Firm::with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('org_name', 'like', '%' . $search . '%')
                        ->orWhere('org_type', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('contact_person', 'like', '%' . $search . '%')
                        ->orWhere('designation', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when(in_array($status, ['pending', 'active', 'blocked'], true), function ($query) use ($status) {
                $query->whereHas('user', function ($userQuery) use ($status) {
                    $userQuery->where('status', $status);
                });
            })
            ->latest()
            ->get();

        return view('admin.manage-firm', compact('firms'));
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:pending,active,blocked',
            'org_name' => 'required|string|max:255',
            'org_type' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'verification_doc' => 'required|file|mimes:pdf,jpeg,jpg,png|max:4096',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'firm',
                'status' => $request->status,
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('firms/photos', 'public');
            }

            $verificationDocPath = $request->file('verification_doc')->store('firms/verification-docs', 'public');

            Firm::create([
                'user_id' => $user->id,
                'org_name' => $request->org_name,
                'org_type' => $request->org_type,
                'photo' => $photoPath,
                'contact_person' => $request->contact_person,
                'designation' => $request->designation,
                'phone' => $request->phone,
                'verification_doc' => $verificationDocPath,
                'address' => $request->address,
            ]);
        });

        return redirect()->route('admin.firms.index')->with('success', 'Firm registered successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $firms = Firm::with('user')->latest()->get();
        $editFirm = Firm::with('user')->findOrFail($id);
        $viewOnly = true;

        return view('admin.manage-firm', compact('firms', 'editFirm', 'viewOnly'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $firms = Firm::with('user')->latest()->get();
        $isDeleteMode = $request->query('mode') === 'delete';

        if (!$isDeleteMode) {
            $editFirm = Firm::with('user')->findOrFail($id);
            return view('admin.manage-firm', compact('firms', 'editFirm'));
        }

        $deleteFirm = Firm::with('user')->findOrFail($id);
        return view('admin.manage-firm', compact('firms', 'deleteFirm', 'isDeleteMode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $firm = Firm::with('user')->findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $firm->user->id,
            'status' => 'required|in:pending,active,blocked',
            'org_name' => 'required|string|max:255',
            'org_type' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'verification_doc' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:4096',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $firm) {
            $firm->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->status,
            ]);

            if ($request->filled('password')) {
                $firm->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $updateData = [
                'org_name' => $request->org_name,
                'org_type' => $request->org_type,
                'contact_person' => $request->contact_person,
                'designation' => $request->designation,
                'phone' => $request->phone,
                'address' => $request->address,
            ];

            if ($request->hasFile('photo')) {
                if ($firm->photo && Storage::disk('public')->exists($firm->photo)) {
                    Storage::disk('public')->delete($firm->photo);
                }
                $updateData['photo'] = $request->file('photo')->store('firms/photos', 'public');
            }

            if ($request->hasFile('verification_doc')) {
                if ($firm->verification_doc && Storage::disk('public')->exists($firm->verification_doc)) {
                    Storage::disk('public')->delete($firm->verification_doc);
                }
                $updateData['verification_doc'] = $request->file('verification_doc')->store('firms/verification-docs', 'public');
            }

            $firm->update($updateData);
        });

        return redirect()->route('admin.firms.index')->with('success', 'Firm updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $firm = Firm::with('user')->findOrFail($id);
        $user = $firm->user;

        DB::transaction(function () use ($firm, $user) {
            if ($firm->photo && Storage::disk('public')->exists($firm->photo)) {
                Storage::disk('public')->delete($firm->photo);
            }

            if ($firm->verification_doc && Storage::disk('public')->exists($firm->verification_doc)) {
                Storage::disk('public')->delete($firm->verification_doc);
            }

            $firm->delete();
            $user->delete();
        });

        return redirect()->route('admin.firms.index')->with('success', 'Firm and associated account deleted successfully!');
    }
}
