<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Enrollment;

class GeminiChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $userMessage = $request->input('message');
        
        // 1. Determine Context and Fetch Relevant Data Dynamically
        $dbContext = "";
        
        if (Auth::check()) {
            $user = Auth::user();
            // Fetch real student course tracking data from your database schemas
            $recentEnrollments = Enrollment::where('user_id', $user->id)
                ->with('course')
                ->latest()
                ->take(3)
                ->get()
                ->map(fn($e) => $e->course->title)
                ->implode(', ');

            $dbContext = "The active user is LOGGED IN. Name: {$user->name}, ID: {$user->id}. Recent courses they joined: " . ($recentEnrollments ?: "None yet.") . ".";
        } else {
            $dbContext = "The active user is a GUEST (Not Logged In). You can only guide them on public parameters. If they ask personal data, tell them to log in.";
        }

        // Add context for public parameters (e.g. general areas or courses available)
        $availableLocations = Course::distinct()->pluck('venue')->implode(', ');
        $dbContext .= " Available course venues/locations in system: " . ($availableLocations ?: "Angamaly, Kochi") . ".";

        // 2. Format System Instructions for Gemini
        $systemInstruction = "You are the official AI Assistant for 'EduConnect', a Multi-Institutional Offline Course Enrollment platform. " .
            "Use the provided database context to give highly precise answers. Avoid hallucinating. " . $dbContext;

        try {
            // 3. Make Secure Payload Request to Gemini API
            $apiKey = env('GEMINI_API_KEY');
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $userMessage]]]
                ],
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $replyText = $result['candidates'][0]['content']['parts'][0]['text'] ?? "I'm having trouble analyzing that request.";
                
                return response()->json([
                    'status' => 'success',
                    'reply' => $replyText
                ]);
            }

            return response()->json(['status' => 'error', 'reply' => 'Connection to AI failed.'], 500);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'reply' => 'An unexpected error occurred.'], 500);
        }
    }
}