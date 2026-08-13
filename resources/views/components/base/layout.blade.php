<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400..600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">


    @stack('styles')
    <title>{{ $title ?? 'EduConnect' }}</title>
    
    
    <style>
        :root {
            --navy: #0f172a;
            --gold: #c9a84c;
            --primary: #2563eb;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* ==========================================================================
           GLOBAL LOADER - EDUCONNECT MODERN
           ========================================================================== */
        .edu-loader {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            z-index: 99999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.35s ease, visibility 0.35s ease;
        }

        .edu-loader.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .edu-loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }

        .edu-loader-icon-wrap {
            position: relative;
            width: 72px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .edu-loader-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: var(--primary);
            border-right-color: var(--primary);
            animation: spin 0.8s linear infinite;
        }

        .edu-loader-ring-inner {
            position: absolute;
            inset: 10px;
            border-radius: 50%;
            border: 2px solid transparent;
            border-bottom-color: var(--gold);
            border-left-color: var(--gold);
            animation: spin 1.2s linear infinite reverse;
        }

        .edu-loader-icon {
            position: relative;
            z-index: 2;
            animation: pulse-icon 1.6s ease-in-out infinite;
        }

        .edu-loader-icon i {
            font-size: 28px;
            color: var(--navy);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes pulse-icon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.12); }
        }

        .edu-loader-brand {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: -0.02em;
        }

        .edu-loader-brand span {
            color: var(--primary);
        }

        .edu-loader-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--navy);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            opacity: 0.7;
            animation: fade-text 1.5s ease-in-out infinite;
            margin: 0;
        }

        @keyframes fade-text {
            0%, 100% { opacity: 0.4; }
            50% { opacity: 0.9; }
        }

        .edu-loader-dots {
            display: flex;
            gap: 6px;
        }

        .edu-loader-dots span {
            width: 6px;
            height: 6px;
            background: var(--primary);
            border-radius: 50%;
            animation: bounce 1.2s ease-in-out infinite;
        }

        .edu-loader-dots span:nth-child(1) { animation-delay: 0s; }
        .edu-loader-dots span:nth-child(2) { animation-delay: 0.15s; }
        .edu-loader-dots span:nth-child(3) { animation-delay: 0.3s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0.5); opacity: 0.3; }
            40% { transform: scale(1.4); opacity: 1; }
        }

        /* ==========================================================================
           GLOBAL SKELETON SHIMMER LOADER FOR IMAGES
           ========================================================================== */
        .img-skeleton {
            background: #f1f5f9;
            background-image: linear-gradient(
                90deg, 
                #f1f5f9 0px, 
                #e2e8f0 50px, 
                #f1f5f9 100px
            );
            background-size: 200% 100%;
            animation: imgShimmer 1.5s infinite linear;
            transition: background 0.4s ease;
        }

        @keyframes imgShimmer {
            0% { background-position: -100% 0; }
            100% { background-position: 100% 0; }
        }

        /* Clears backdrops smoothly when images arrive */
        .img-skeleton-loaded {
            background: transparent !important;
            background-image: none !important;
            animation: none !important;
        }


    /* Fix DataTables container alignment */
    div.dt-buttons {
        float: none !important;
        margin-bottom: 0 !important;
    }

    /* Remove default DataTables dark background wrappers */
    .dt-buttons .btn,
    div.dt-button-collection .btn {
        background-image: none !important;
        box-shadow: none !important;
        border-radius: 0.375rem !important;
        margin-left: 0.25rem !important;
    }

    /* Ensure crisp text & icon contrast on hover */
    .dt-buttons .btn-outline-secondary:hover,
    .dt-buttons .btn-outline-success:hover,
    .dt-buttons .btn-outline-info:hover,
    .dt-buttons .btn-outline-danger:hover,
    .dt-buttons .btn-outline-dark:hover {
        color: #fff !important;
    }
    </style>
</head>
<body>

    <div id="globalLoader" class="edu-loader hidden">
        <div class="edu-loader-content">
            <div class="edu-loader-icon-wrap">
                <div class="edu-loader-ring"></div>
                <div class="edu-loader-ring-inner"></div>
                <div class="edu-loader-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
            </div>
            
            <div class="edu-loader-brand">Edu<span>Connect</span></div>
            <p class="edu-loader-text">Preparing your experience</p>
            
            <div class="edu-loader-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    {{ $slot }}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<!-- jQuery (Required by DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables Core & JS Dependencies -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTables Buttons Extensions for PDF, Excel, Print & Copy -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const loader = document.getElementById('globalLoader');

        // Global functions usable anywhere in your app context
        window.showLoader = function() {
            loader.classList.remove('hidden');
        };

        window.hideLoader = function() {
            loader.classList.add('hidden');
        };

        // 1. Intercept Standard Form Submissions
        document.addEventListener("submit", function (e) {
            // Skip chat forms, Alpine.js forms, and AJAX forms
            const form = e.target;
            if (
                form.hasAttribute('data-remote') || 
                form.matches('[id^="ajax"]') ||
                form.closest('[x-data]') ||          // Alpine.js forms
                form.classList.contains('chat-form')  // Chat forms
            ) {
                return;
            }
            window.showLoader();
        });

        // 2. Intercept Sidebar/Navbar Link Clicks
        document.addEventListener("click", function (e) {
            const link = e.target.closest("a");
            
            if (!link || !link.href) return;

            const hrefAttr = link.getAttribute('href');

            // Skip these types of links
            if (
                !hrefAttr ||
                hrefAttr === '#' || 
                hrefAttr.startsWith('#') || 
                hrefAttr.startsWith('javascript:') ||
                link.hasAttribute('data-bs-toggle') || 
                link.classList.contains('dropdown-toggle') || 
                link.target === "_blank" ||
                link.hasAttribute('download') ||
                link.closest('[x-data]') ||           // Alpine.js elements
                link.closest('.chat-contact') ||       // Chat sidebar contacts
                link.closest('.chat-input')            // Chat input area
            ) {
                return; 
            }

            window.showLoader();
        });

        // 3. Force Hide Loader when hitting the browser's Back/Forward button
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.hideLoader();
            }
        });

        // ==================================================================
        // 4. AUTOMATED GLOBAL IMAGE SKELETON OBSERVER SYSTEM
        // ==================================================================
        function setupImageSkeleton(img) {
            if (img.hasAttribute('data-no-skeleton') || (img.width > 0 && img.width < 15)) {
                return;
            }

            if (!img.complete) {
                img.classList.add('img-skeleton');

                img.addEventListener('load', function () {
                    img.classList.add('img-skeleton-loaded');
                }, { once: true });

                img.addEventListener('error', function () {
                    img.classList.add('img-skeleton-loaded');
                }, { once: true });
            } else {
                img.classList.add('img-skeleton-loaded');
            }
        }

        document.querySelectorAll('img').forEach(setupImageSkeleton);

        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.tagName === 'IMG') {
                        setupImageSkeleton(node);
                    } else if (node.querySelectorAll) {
                        node.querySelectorAll('img').forEach(setupImageSkeleton);
                    }
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    });
</script>

@stack('scripts')

  @include('components.common.chatbot')
</body>
</html>