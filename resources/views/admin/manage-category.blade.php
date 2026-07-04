<x-admin.layout active="categories">

 @php
    $isEdit = isset($editCategory) && !isset($viewOnly);
    $isView = isset($editCategory) && isset($viewOnly);
    $isCreate = !isset($editCategory);
@endphp    

<x-admin.filter-card
    title="Search Categories"
    action="{{ route('admin.categories.index') }}"
    search-value="{{ request('search') }}"
    search-placeholder="Search by category name, slug, or description"
    reset-url="{{ route('admin.categories.index') }}"
/>

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="bi bi-plus-lg"></i> Add New Category
    </button>

    <div class="card mt-4 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Existing Categories</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($category->icon && str_contains($category->icon, '/'))
                                    <img src="{{ asset('storage/' . $category->icon) }}"
                                         class="rounded me-2" width="42" height="42" style="object-fit: cover;" alt="Category Icon">
                                @else
                                    <span class="d-inline-flex align-items-center justify-content-center rounded me-2 bg-light border" style="width: 42px; height: 42px;">
                                        <i class="bi {{ $category->icon ?? 'bi-collection' }}"></i>
                                    </span>
                                @endif
                                {{ $category->name ?? 'Unknown Category' }}
                            </div>
                        </td>
                        <td>{{ $category->slug ?? 'N/A' }}</td>
                        <td>{{ $category->description ?? 'N/A' }}</td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm gap-1">
                                <a href="{{ route('admin.categories.show', $category->id) }}">
                                <button class="btn btn-sm btn-outline-primary" title="View Profile">
                                    <i class="bi bi-eye"></i>
                                </button>
                                </a>
                                <a href="{{ route('admin.categories.edit', $category->id) }}">
                                <button class="btn btn-sm btn-outline-warning" title="Edit Category">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </a>
                                <a href="{{ route('admin.categories.edit', [$category->id, 'mode' => 'delete']) }}">
                                <button class="btn btn-sm btn-outline-danger" title="Delete Category">
                                    <i class="bi bi-trash"></i>
                                </button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                            No categories yet. Click "Add New Category" to begin.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



  
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                   <h5 class="modal-title">
    {{ $isEdit ? 'Edit Category: ' . $editCategory->name : ($isView ? 'Category : ' . $editCategory->name : 'Add New Category') }}
</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ isset($editCategory) ? route('admin.categories.update', $editCategory->id) : route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($editCategory))
                        @method('PATCH')
                    @endif
                    <div class="modal-body">
                        
                        {{-- 1. Profile Image Preview (Visible in Edit/View) --}}
                            @if(!$isCreate)
                                <div class="text-center mb-4">
                                    @if($editCategory->icon && str_contains($editCategory->icon, '/'))
                                        <img src="{{ asset('storage/' . $editCategory->icon) }}"
                                             class="rounded img-thumbnail shadow-sm"
                                             style="width: 120px; height: 120px; object-fit: cover;" alt="Category Icon">
                                    @else
                                        <div class="d-inline-flex align-items-center justify-content-center rounded img-thumbnail shadow-sm bg-light"
                                             style="width: 120px; height: 120px;">
                                            <i class="bi {{ $editCategory->icon ?? 'bi-collection' }}" style="font-size: 2rem;"></i>
                                        </div>
                                    @endif
                                    @if($isView)
                                        <h4 class="mt-2">{{ $editCategory->name }}</h4>
                                        {{-- <span class="badge bg-success">Active Category</span> --}}
                                    @endif
                                </div>
                                
                            @endif

                        <div class="row g-3">
                            <h6 class="border-bottom pb-2">Category Information</h6>
                            <div class="col-md-6">
                                <label class="form-label">Category Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $editCategory->name ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                            </div>
                            @if ($isView)
                            <div class="col-md-6">
                                <label class="form-label">Category Slug</label>
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $editCategory->slug ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                            </div>
                                
                            @endif

                            @if (!$isView)
                                 <div class="col-md-6">
                                <label class="form-label">Category Icon</label>
                                <input type="file" name="icon" class="form-control @error('icon') is-invalid @enderror">
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                            <div class="col-md-12">
                                <label class="form-label">Category Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2" {{ $isView ? 'disabled' : '' }}>{{ old('description', $editCategory->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                        </div>
                    </div>
                  
                        
                  
                    <div class="modal-footer">
                                @if(isset($editCategory))
                                    {{-- If editing, link physically back to the index --}}
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                                @else
                                    {{-- If creating, just close the modal normally --}}
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                @endif
                                
                                  @if (!$isView)
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($editCategory) ? 'Update Category' : 'Create Category' }}
                                </button>
                                 @endif
                    </div>
                     
                </form>
            </div>
        </div>
    </div>



@if(isset($deleteCategory) && isset($isDeleteMode) && $isDeleteMode)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Category</h5>
                    <a href="{{ route('admin.categories.index') }}" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body text-center">
                    <p>Delete <strong>{{ $deleteCategory->name ?? 'Unknown Category' }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('admin.categories.destroy', $deleteCategory->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();

            var myModalDelete = document.getElementById('deleteModal');
        
        // Listen for the modal being hidden
        myModalDelete.addEventListener('hidden.bs.modal', function () {
            // Check if we are currently on an "edit" URL
            if (window.location.pathname.includes('/edit')) {
                // Redirect back to the main index to clean the URL
                window.location.href = "{{ route('admin.categories.index') }}";
            }
        });

        });
    </script>
@endif



     <x-toast />
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myModalElement = document.getElementById('addCategoryModal');
            var myModal = new bootstrap.Modal(myModalElement);
            myModal.show();
        });
    </script>
@endif
{{-- edit modal show --}}
@if(isset($editCategory))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            editModal.show();
       
        var myModalElement = document.getElementById('addCategoryModal');
        
        // Listen for the modal being hidden
        myModalElement.addEventListener('hidden.bs.modal', function () {
            // Check if we are currently on an "edit" URL
            if (window.location.pathname.includes('/edit') || window.location.pathname.match(/\d+$/)) {
                // Redirect back to the main index to clean the URL
                window.location.href = "{{ route('admin.categories.index') }}";
            }
        });

    });

   
    </script>
@endif

</x-admin.layout>