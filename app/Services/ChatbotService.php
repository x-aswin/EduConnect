<?php

namespace App\Services;

use App\Models\Course;
use Carbon\Carbon;

class ChatbotService
{
    /**
     * Main entry point — process any user message.
     */
    public function process(string $message): array
    {
        $message = strtolower(trim($message));

        // Help
        if ($this->hasWord($message, ['help', 'what can you', 'commands', 'how to'])) {
            return $this->help();
        }

        // Greetings
        if ($this->hasWord($message, ['hi', 'hello', 'hey', 'good morning', 'good evening'])) {
            return $this->text("Hello! 👋 I can help you find courses. Try asking like:\n\n• \"linux courses near angamaly\"\n• \"free python courses\"\n• \"upcoming courses this month\"\n\nOr type **help** to see everything I can do.");
        }

        // Upcoming courses
        if ($this->hasWord($message, ['upcoming', 'this month', 'next month', 'this week', 'next week', 'starting soon'])) {
            return $this->upcomingCourses($message);
        }

        // Default: search courses
        return $this->searchCourses($message);
    }

    // ─── Course Search ────────────────────────────────────────

    private function searchCourses(string $message): array
    {
        $query = Course::with('college')->where('status', 'active');

        // Extract topic keywords
        $keywords = $this->extractKeywords($message);
        if (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('title', 'like', "%{$word}%")
                      ->orWhere('description', 'like', "%{$word}%");
                }
            });
        }

        // Location filter
        $location = $this->extractLocation($message);
        if ($location) {
            $query->where(function ($q) use ($location) {
                $q->where('venue', 'like', "%{$location}%")
                  ->orWhereHas('college', fn($c) => $c->where('address', 'like', "%{$location}%"));
            });
        }

        // Time filter
        if ($this->hasWord($message, ['this month'])) {
            $query->whereMonth('start_date', now()->month);
        }
        if ($this->hasWord($message, ['next month'])) {
            $query->whereMonth('start_date', now()->addMonth()->month);
        }

        // Type filter
        if ($this->hasWord($message, ['firm', 'corporate', 'company', 'organisation'])) {
            $query->where('course_type', 'firm_only');
        } elseif ($this->hasWord($message, ['student', 'individual'])) {
            $query->where('course_type', 'student_only');
        }

        // Price filter
        if ($this->hasWord($message, ['free', 'no cost', 'no fee'])) {
            $query->where('price', 0);
        }

        $courses = $query->take(5)->get();

        if ($courses->isEmpty()) {
            return $this->text("I couldn't find any courses matching your search. Try:\n• Different keywords\n• A different location\n• Checking 'upcoming courses'\n\nOr type **help** for more options.");
        }

        return [
            'type'    => 'courses',
            'message' => "Found **{$courses->count()}** course(s):",
            'courses' => $courses->map(fn($c) => [
                'title'   => $c->title,
                'college' => $c->college->institution_name ?? 'N/A',
                'venue'   => $c->venue ?? 'TBA',
                'start'   => $c->start_date ? Carbon::parse($c->start_date)->format('M d, Y') : 'TBA',
                'price'   => $c->price == 0 ? 'Free' : '₹' . number_format($c->price),
                'type'    => $c->course_type === 'student_only' ? 'Student' : 'Firm',
                'url'     => route('course.show', $c->slug),
            ])->toArray(),
        ];
    }

    // ─── Upcoming Courses ─────────────────────────────────────

    private function upcomingCourses(string $message): array
    {
        $query = Course::with('college')
            ->where('status', 'active')
            ->whereNotNull('start_date')
            ->where('start_date', '>=', now())
            ->orderBy('start_date');

        if ($this->hasWord($message, ['this month'])) {
            $query->whereMonth('start_date', now()->month);
        }
        if ($this->hasWord($message, ['next month'])) {
            $query->whereMonth('start_date', now()->addMonth()->month);
        }

        $courses = $query->take(5)->get();

        if ($courses->isEmpty()) {
            return $this->text("No upcoming courses found. Check back later!");
        }

        return [
            'type'    => 'courses',
            'message' => "Here are upcoming courses:",
            'courses' => $courses->map(fn($c) => [
                'title'   => $c->title,
                'college' => $c->college->institution_name ?? 'N/A',
                'venue'   => $c->venue ?? 'TBA',
                'start'   => Carbon::parse($c->start_date)->format('M d, Y'),
                'price'   => $c->price == 0 ? 'Free' : '₹' . number_format($c->price),
                'url'     => route('course.show', $c->slug),
            ])->toArray(),
        ];
    }

    // ─── Help ─────────────────────────────────────────────────

    private function help(): array
    {
        return $this->text(
            "**I can help you with:**\n\n" .
            "🔍 **Find courses** — \"python courses near kochi\", \"free courses\"\n" .
            "📅 **Upcoming** — \"courses starting this month\"\n" .
            "💼 **Corporate** — \"firm courses in angamaly\"\n" .
            "📍 **Location** — \"courses near ernakulam\"\n\n" .
            "Just type naturally and I'll do my best!"
        );
    }

    // ─── Helpers ──────────────────────────────────────────────

    private function text(string $message): array
    {
        return ['type' => 'text', 'message' => $message];
    }

    private function hasWord(string $message, array $words): bool
    {
        foreach ($words as $word) {
            if (str_contains($message, $word)) return true;
        }
        return false;
    }

    private function extractKeywords(string $message): array
    {
        $stopWords = ['course', 'courses', 'show', 'me', 'find', 'search', 'looking', 'for', 'the', 'a', 'an', 'in', 'at', 'near', 'this', 'month', 'next', 'training', 'class', 'program', 'free', 'corporate', 'firm', 'student', 'starting', 'upcoming'];
        $words = explode(' ', $message);
        $filtered = array_filter($words, fn($w) => !in_array($w, $stopWords) && strlen($w) > 2);
        return array_values($filtered);
    }

    private function extractLocation(string $message): ?string
    {
        $locations = [
            'angamaly', 'kochi', 'ernakulam', 'thrissur', 'alwaye', 'aluva',
            'kalady', 'kottayam', 'trivandrum', 'calicut', 'kannur'
        ];
        foreach ($locations as $loc) {
            if (str_contains($message, $loc)) return $loc;
        }
        return null;
    }
}