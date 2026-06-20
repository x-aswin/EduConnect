<x-firm.layout title="My Groups - EduConnect" active="groups">
    @push('styles')
    <style>
        .page-header {
            background: linear-gradient(135deg, #1e3ce0 0%, #295b69ff 100%);
            border-radius: 2rem;
            color: white;
            padding: 2rem 2.5rem;
            box-shadow: 0 15px 30px rgba(79, 110, 246, 0.15);
            margin-bottom: 2rem;
        }

        .group-card {
            background: white;
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .group-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .group-card .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .group-card .card-body {
            padding: 1.5rem;
        }

        .member-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .member-table td {
            padding: 0.85rem 1rem;
            color: #334155;
            font-size: 0.9rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .member-table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge-active {
            background-color: #dcfce7;
            color: #15803d;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.3rem 0.75rem;
            border-radius: 50px;
        }

        .badge-inactive {
            background-color: #fee2e2;
            color: #b91c1c;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.3rem 0.75rem;
            border-radius: 50px;
        }

        .badge-count {
            background-color: #eef2ff;
            color: #3730a3;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.3rem 0.75rem;
            border-radius: 50px;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.2s;
            font-size: 0.85rem;
        }

        .btn-icon:hover {
            transform: scale(1.05);
        }

        .btn-edit {
            background: #eef2ff;
            color: #4338ca;
        }

        .btn-edit:hover {
            background: #4338ca;
            color: white;
        }

        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: white;
        }

        .btn-remove-member {
            background: #fff7ed;
            color: #ea580c;
        }

        .btn-remove-member:hover {
            background: #ea580c;
            color: white;
        }

        .add-member-row {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            margin-top: 1rem;
        }

        .add-member-row .form-control {
            border-radius: 10px;
            font-size: 0.875rem;
            border: 1px solid #e2e8f0;
        }

        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.4;
        }

        .member-input-row {
            background: #f8fafc;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border: 1px solid #e2e8f0;
        }

        .modal-content {
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid #f1f5f9;
            padding: 1.5rem;
            border-radius: 1.5rem 1.5rem 0 0;
        }

        .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
            border-radius: 0 0 1.5rem 1.5rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #4f6ef6;
            box-shadow: 0 0 0 3px rgba(79, 110, 246, 0.1);
        }

        .btn-add-group {
            background: linear-gradient(135deg, #1e3ce0, #4f6ef6);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            transition: all 0.2s;
        }

        .btn-add-group:hover {
            background: linear-gradient(135deg, #1a34c4, #4460e0);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(79, 110, 246, 0.35);
        }
    </style>
    @endpush

    {{-- Success Alert --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mt-3" role="alert" id="successAlert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="page-header mt-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <span class="badge bg-white bg-opacity-20 text-black px-3 py-2 rounded-pill mb-2">
                    <i class="bi bi-people-fill me-1"></i> Group Management
                </span>
                <h1 class="fw-bold mb-1 mt-2">My Groups</h1>
                <p class="text-white text-opacity-75 mb-0">Create and manage reusable participant groups for course enrollments.</p>
            </div>
            <button class="btn-add-group btn" data-bs-toggle="modal" data-bs-target="#addGroupModal" id="openAddGroupModalBtn">
                <i class="bi bi-plus-lg me-1"></i> Add New Group
            </button>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert" id="validationErrorAlert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Groups List --}}
    @if ($groups->isEmpty())
        <div class="empty-state">
            <i class="bi bi-people d-block"></i>
            <h4 class="fw-semibold text-secondary">No groups yet</h4>
            <p class="text-muted">Add your first group to get started.</p>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addGroupModal">
                <i class="bi bi-plus-lg me-1"></i> Add New Group
            </button>
        </div>
    @else
        @foreach ($groups as $group)
            <div class="group-card">
                <div class="card-header">
                    <h5 class="fw-bold mb-0 me-auto text-dark">
                        <i class="bi bi-people-fill me-2 text-primary"></i>{{ $group->group_name }}
                    </h5>
                    <span class="badge-count">{{ $group->members->count() }} {{ Str::plural('member', $group->members->count()) }}</span>
                    @if ($group->is_active)
                        <span class="badge-active"><i class="bi bi-check-circle-fill me-1"></i>Active</span>
                    @else
                        <span class="badge-inactive"><i class="bi bi-x-circle-fill me-1"></i>Inactive</span>
                    @endif
                    <button
                        class="btn-icon btn-edit btn ms-1"
                        data-bs-toggle="modal"
                        data-bs-target="#editGroupModal"
                        data-id="{{ $group->id }}"
                        data-name="{{ $group->group_name }}"
                        data-active="{{ $group->is_active ? '1' : '0' }}"
                        title="Edit Group"
                    >
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button
                        class="btn-icon btn-delete btn ms-1"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteGroupModal"
                        data-id="{{ $group->id }}"
                        data-name="{{ $group->group_name }}"
                        title="Delete Group"
                    >
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>

                <div class="card-body">
                    @if ($group->members->isEmpty())
                        <p class="text-muted fst-italic mb-3">No members yet.</p>
                    @else
                        <div class="table-responsive rounded-3 mb-3">
                            <table class="table member-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Contact Info</th>
                                        <th style="width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group->members as $member)
                                        <tr>
                                            <td class="fw-semibold">{{ $member->name }}</td>
                                            <td>{{ $member->contact_info }}</td>
                                            <td>
                                                <form
                                                    method="POST"
                                                    action="{{ route('firm.groups.members.destroy', [$group->id, $member->id]) }}"
                                                    onsubmit="return confirm('Remove {{ addslashes($member->name) }} from this group?')"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-icon btn-remove-member btn" title="Remove Member">
                                                        <i class="bi bi-person-dash-fill"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Inline Add Member Form --}}
                    <div class="add-member-row">
                        <form method="POST" action="{{ route('firm.groups.members.store', $group->id) }}" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary mb-1">Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Member name"
                                    required
                                    id="addMemberName{{ $group->id }}"
                                >
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary mb-1">Contact Info</label>
                                <input
                                    type="text"
                                    name="contact_info"
                                    class="form-control"
                                    placeholder="Email or phone"
                                    required
                                    id="addMemberContact{{ $group->id }}"
                                >
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 rounded-3" style="padding: 0.6rem;">
                                    <i class="bi bi-plus-lg me-1"></i> Add
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- ========================
         Add Group Modal
         ======================== --}}
    <div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addGroupModalLabel">
                        <i class="bi bi-people-fill me-2 text-primary"></i> Add New Group
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('firm.groups.store') }}" id="addGroupForm">
                    @csrf
                    <div class="modal-body p-4">
                        {{-- Group Name --}}
                        <div class="mb-4">
                            <label for="group_name" class="form-label fw-semibold">Group Name <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="group_name"
                                id="group_name"
                                class="form-control @error('group_name') is-invalid @enderror"
                                value="{{ old('group_name') }}"
                                placeholder="e.g. Sales Team Q3, Engineering Batch 2"
                                required
                            >
                            @error('group_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Members Section --}}
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <label class="fw-semibold mb-0">Members <span class="text-muted fw-normal">(optional)</span></label>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="addMemberRowBtn">
                                    <i class="bi bi-plus-lg me-1"></i> Add Member
                                </button>
                            </div>
                            <div id="membersContainer">
                                {{-- Member rows will be injected here --}}
                            </div>
                            <p class="text-muted small mt-2 mb-0" id="noMembersNote">
                                <i class="bi bi-info-circle me-1"></i> No members added yet. Click "+ Add Member" to add participants.
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Create Group
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================
         Edit Group Modal
         ======================== --}}
    <div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editGroupModalLabel">
                        <i class="bi bi-pencil-fill me-2 text-primary"></i> Edit Group
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" id="editGroupForm">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label for="edit_group_name" class="form-label fw-semibold">Group Name <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="group_name"
                                id="edit_group_name"
                                class="form-control"
                                required
                            >
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="edit_is_active" value="1">
                            <label class="form-check-label fw-semibold" for="edit_is_active">Active</label>
                            <small class="d-block text-muted mt-1">Inactive groups won't appear in enrollment selection.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================
         Delete Group Modal
         ======================== --}}
    <div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="deleteGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center px-4 pb-2">
                    <div class="mb-3" style="font-size: 3rem; line-height: 1;">
                        <span style="background: #fee2e2; border-radius: 50%; width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="bi bi-trash3-fill text-danger" style="font-size: 2rem;"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold">Delete Group</h5>
                    <p class="text-muted mb-0">Are you sure you want to delete the group <strong id="deleteGroupName"></strong>? All members in this group will also be removed. This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                    <form method="POST" action="" id="deleteGroupForm">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-light rounded-pill px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                            <i class="bi bi-trash3 me-1"></i> Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ─── Dynamic member rows in Add Group modal ───────────────────────────────
        let memberIndex = 0;

        const membersContainer = document.getElementById('membersContainer');
        const noMembersNote    = document.getElementById('noMembersNote');

        function updateNoMembersNote() {
            noMembersNote.style.display = membersContainer.children.length === 0 ? 'block' : 'none';
        }

        document.getElementById('addMemberRowBtn').addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'member-input-row d-flex align-items-end gap-2';
            row.innerHTML = `
                <div class="flex-fill">
                    <label class="form-label small fw-semibold text-secondary mb-1">Name *</label>
                    <input type="text" name="members[${memberIndex}][name]" class="form-control" placeholder="Member name" required>
                </div>
                <div class="flex-fill">
                    <label class="form-label small fw-semibold text-secondary mb-1">Contact Info *</label>
                    <input type="text" name="members[${memberIndex}][contact_info]" class="form-control" placeholder="Email or phone" required>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-icon btn-remove-member remove-member-row" title="Remove" style="width:34px;height:34px;">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;
            row.querySelector('.remove-member-row').addEventListener('click', function () {
                row.remove();
                updateNoMembersNote();
            });
            membersContainer.appendChild(row);
            memberIndex++;
            updateNoMembersNote();
        });

        // ─── Edit Group Modal ─────────────────────────────────────────────────────
        document.getElementById('editGroupModal').addEventListener('show.bs.modal', function (event) {
            const btn      = event.relatedTarget;
            const id       = btn.dataset.id;
            const name     = btn.dataset.name;
            const isActive = btn.dataset.active === '1';

            document.getElementById('edit_group_name').value = name;
            document.getElementById('edit_is_active').checked = isActive;
            document.getElementById('editGroupForm').action = `/firm/groups/${id}`;
        });

        // ─── Delete Group Modal ───────────────────────────────────────────────────
        document.getElementById('deleteGroupModal').addEventListener('show.bs.modal', function (event) {
            const btn  = event.relatedTarget;
            const id   = btn.dataset.id;
            const name = btn.dataset.name;

            document.getElementById('deleteGroupName').textContent = name;
            document.getElementById('deleteGroupForm').action = `/firm/groups/${id}`;
        });

        // ─── Reopen Add Group modal if there are validation errors ────────────────
        @if ($errors->any())
            var addGroupModal = new bootstrap.Modal(document.getElementById('addGroupModal'));
            addGroupModal.show();
        @endif
    </script>
    @endpush
</x-firm.layout>
