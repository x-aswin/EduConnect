<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\FirmGroup;
use App\Models\FirmGroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $firm = Auth::user()->firm;
        $groups = FirmGroup::where('firm_id', $firm->id)
                           ->with('members')
                           ->latest()
                           ->get();

        return view('firm.manage-groups', compact('groups', 'firm'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'group_name'               => 'required|string|max:255',
            'members'                  => 'nullable|array',
            'members.*.name'           => 'required|string|max:255',
            'members.*.contact_info'   => 'required|string|max:255',
        ]);

        $firm = Auth::user()->firm;

        DB::transaction(function () use ($request, $firm) {
            $group = FirmGroup::create([
                'firm_id'    => $firm->id,
                'group_name' => $request->group_name,
                'is_active'  => true,
            ]);

            if ($request->filled('members')) {
                foreach ($request->members as $member) {
                    FirmGroupMember::create([
                        'group_id'     => $group->id,
                        'name'         => $member['name'],
                        'contact_info' => $member['contact_info'],
                    ]);
                }
            }
        });

        return redirect()->route('firm.groups.index')
                         ->with('success', 'Group created successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'is_active'  => 'boolean',
        ]);

        $firm = Auth::user()->firm;
        $group = FirmGroup::where('firm_id', $firm->id)->findOrFail($id);

        $group->update([
            'group_name' => $request->group_name,
            'is_active'  => $request->boolean('is_active'),
        ]);

        return redirect()->route('firm.groups.index')
                         ->with('success', 'Group updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $firm = Auth::user()->firm;
        $group = FirmGroup::where('firm_id', $firm->id)->findOrFail($id);
        $group->delete();

        return redirect()->route('firm.groups.index')
                         ->with('success', 'Group deleted successfully!');
    }
}
