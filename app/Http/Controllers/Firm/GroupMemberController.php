<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\FirmGroup;
use App\Models\FirmGroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMemberController extends Controller
{
    /**
     * Add a single member to an existing group.
     */
    public function store(Request $request, string $groupId)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
        ]);

        $firm = Auth::user()->firm;
        $group = FirmGroup::where('firm_id', $firm->id)->findOrFail($groupId);

        FirmGroupMember::create([
            'group_id'     => $group->id,
            'name'         => $request->name,
            'contact_info' => $request->contact_info,
        ]);

        return redirect()->route('firm.groups.index')
                         ->with('success', 'Member added successfully!');
    }

    /**
     * Remove a member from a group.
     */
    public function destroy(string $groupId, string $memberId)
    {
        $firm = Auth::user()->firm;
        $group = FirmGroup::where('firm_id', $firm->id)->findOrFail($groupId);

        FirmGroupMember::where('group_id', $group->id)->findOrFail($memberId)->delete();

        return redirect()->route('firm.groups.index')
                         ->with('success', 'Member removed successfully!');
    }
}
