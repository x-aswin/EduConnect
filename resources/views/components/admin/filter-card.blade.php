@props([
    'title',
    'action',
    'searchValue' => '',
    'searchPlaceholder' => 'Search...',
    'searchName' => 'search',
    'method' => 'GET',
    'resetUrl' => null,
])

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form action="{{ $action }}" method="{{ $method }}">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label fw-semibold">{{ $title }}</label>
                    <input
                        type="search"
                        name="{{ $searchName }}"
                        class="form-control"
                        placeholder="{{ $searchPlaceholder }}"
                        value="{{ $searchValue }}"
                    >
                </div>

                {{ $filters ?? '' }}

                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                    @if($resetUrl)
                        <a href="{{ $resetUrl }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>