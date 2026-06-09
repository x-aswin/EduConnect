<x-guest.layout title="Explore Courses - EduConnect" active="coursedetails">
    {{-- <x-common.course-details :course="$course" type="guest"/> --}}
    <x-common.course-details 
    :course="$course" 
    :type="($course['course_type'] ?? $course['type'] ?? '') === 'student_only' ? 'student' : 'firm'"
    :isguest="true"
    />
</x-guest.layout>