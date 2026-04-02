@props(['type' => null, 'message' => null])

@php
    /** * Priority Logic:
     * 1. Manual Props (passed directly to component)
     * 2. Session 'success'
     * 3. Session 'error'
     * 4. Validation Errors ($errors bag)
     */
    $finalType = $type ?? (session('success') ? 'success' : (session('error') || $errors->any() ? 'danger' : 'info'));
    
    $finalMessage = $message 
        ?? session('success') 
        ?? session('error') 
        ?? ($errors->any() ? 'Please check the form for errors.' : null);
@endphp

@if($finalMessage)
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div class="toast show align-items-center text-white bg-{{ $finalType }} border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi {{ $finalType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} me-2"></i> 
                {{ $finalMessage }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Find all toasts in the container and initialize them
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl, { 
                autohide: true, 
                delay: 5000 
            }).show();
        });
    });
</script> --}}
@endif


{{-- 
1. Basic Information (Blue)
Use this for general announcements or status updates that aren't necessarily "Successes."

HTML
<x-toast type="info" message="System maintenance scheduled for 10 PM tonight." />
2. Warning (Yellow)
Perfect for letting the Admin know something needs attention but isn't a "hard error."

HTML
<x-toast type="warning" message="This student has not uploaded a profile photo yet." />
3. Manual Success (Green)
If you are on a page and want to confirm an action happened (without a controller redirect).

HTML
<x-toast type="success" message="Changes saved to draft." />
4. Manual Error (Red)
Use this if you want to hard-code an error message for a specific edge case.

HTML
<x-toast type="danger" message="Access Denied: You do not have permission to delete this record." /> --}}