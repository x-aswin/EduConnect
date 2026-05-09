{{-- resources/views/components/common/college-card.blade.php --}}
@props(['college'])

{{-- Only render if a college instance is provided --}}
@if($college)
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <h5 class="fw-bold mb-3">
        <i class="bi bi-building-fill text-primary me-2"></i>About the College
    </h5>

    <div class="d-flex align-items-start gap-3 mb-3">
        {{-- College logo / photo --}}
        <div class="flex-shrink-0">
            @if(!empty($college->photo))
                <img src="{{ asset('storage/' . ltrim($college->photo, '/')) }}" 
                     alt="{{ $college->institution_name ?? 'College' }}" 
                     class="rounded-circle" width="64" height="64" style="object-fit: cover;">
            @else
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" 
                     style="width: 64px; height: 64px;">
                    <i class="bi bi-building fs-3 text-primary"></i>
                </div>
            @endif
        </div>

        <div>
            <h6 class="fw-bold mb-0">{{ $college->institution_name ?? 'College Name' }}</h6>
            @if($college->user)
                <small class="text-muted">Acronym: {{ $college->user->name ?? 'N/A' }}</small>
            @endif

            {{-- Contact person --}}
            @if($college->contact_person)
                <div class="mt-1">
                    <small class="text-secondary d-block">
                        <i class="bi bi-person-circle me-1"></i> {{ $college->contact_person }}
                        @if($college->designation)
                            · {{ $college->designation }}
                        @endif
                    </small>
                </div>
            @endif
        </div>
    </div>

    {{-- Contact details --}}
    <div class="row g-2 small">
        @if($college->contact_number)
            <div class="col-12">
                <i class="bi bi-telephone-fill text-secondary me-1"></i>
                <strong>{{ $college->contact_number }}</strong>
            </div>
        @endif

        @if($college->college_phone)
            <div class="col-12">
                <i class="bi bi-telephone text-secondary me-1"></i>
                Office: {{ $college->college_phone }}
            </div>
        @endif

        @if($college->user->email)
            <div class="col-12">
                <i class="bi bi-envelope-fill text-secondary me-1"></i>
                <a href="mailto:{{ $college->user->email }}" class="text-decoration-none">{{ $college->user->email }}</a>
            </div>
        @endif

        @if($college->address)
            <div class="col-12 mt-1">
                <i class="bi bi-geo-alt-fill text-secondary me-1"></i>
                <span class="text-secondary">{{ $college->address }}</span>
            </div>
        @endif

        @if($college->website)
            <div class="col-12 mt-1">
                <i class="bi bi-globe2 text-secondary me-1"></i>
                <a href="{{ $college->website }}" target="_blank" class="text-decoration-none">{{ $college->website }}</a>
            </div>
        @endif
    </div>
</div>
@endif