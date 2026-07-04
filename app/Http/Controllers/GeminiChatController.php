<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class GeminiChatController extends Controller
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $userMessage = $request->input('message');
        
        $userStatusContext = "The user is browsing as a Guest.";
        if (Auth::check()) {
            $userStatusContext = "The user is LOGGED IN. Active User Name: " . Auth::user()->name . ", Account ID: " . Auth::id();
        }

        $systemInstruction = "You are the central EduConnect AI Assistant. " . $userStatusContext . " " .
            "You have access to tools to fetch live database values. If the user asks about courses, lookups, or profile data, " .
            "you MUST invoke the matching tool. If a guest tries to access profile or enrollment tools, tell them to sign in.";

        $toolsConfig = [
            [
                'functionDeclarations' => [
                    [
                        'name' => 'search_courses',
                        'description' => 'Search the courses table using flexible pattern matching on title, category, or location.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'The search keyword (e.g., "python", "php").']
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'get_user_profile',
                        'description' => 'Retrieve profile information of the currently authenticated user.',
                        'parameters' => ['type' => 'OBJECT', 'properties' => (object)[]]
                    ],
                    [
                        'name' => 'get_user_enrollments',
                        'description' => 'Fetch a historical list of courses the current student has joined.',
                        'parameters' => ['type' => 'OBJECT', 'properties' => (object)[]]
                    ]
                ]
            ]
        ];

        try {
            // Call 1: See if Gemini wants to use a tool
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->apiKey}", [
                    'contents' => [['parts' => [['text' => $userMessage]]]],
                    'tools' => $toolsConfig,
                    'systemInstruction' => ['parts' => [['text' => $systemInstruction]]]
                ]);

            if (!$response->successful()) {
                return response()->json(['status' => 'error', 'reply' => ['text' => 'Unable to contact AI layer.', 'buttons' => []]]);
            }

            $result = $response->json();
            $firstPart = $result['candidates'][0]['content']['parts'][0] ?? null;

            // If Gemini decides to execute a function tool call
            if (isset($firstPart['functionCall'])) {
                $functionName = $firstPart['functionCall']['name'];
                $args = $firstPart['functionCall']['args'] ?? [];
                $dbOutput = [];

                if ($functionName === 'search_courses') {
                    $searchQuery = $args['query'] ?? '';
                    $dbOutput = Course::where('title', 'like', "%{$searchQuery}%")
                        ->orWhere('category', 'like', "%{$searchQuery}%")
                        ->orWhere('venue', 'like', "%{$searchQuery}%")
                        ->take(5)
                        ->get()
                        ->toArray();
                } elseif ($functionName === 'get_user_profile') {
                    $dbOutput = Auth::check() 
                        ? User::select('id', 'name', 'email')->find(Auth::id())->toArray()
                        : ['error' => 'User is not logged in.'];
                } elseif ($functionName === 'get_user_enrollments') {
                    $dbOutput = Auth::check()
                        ? Enrollment::where('user_id', Auth::id())->with('course:id,title,venue')->latest()->take(5)->get()->toArray()
                        : ['error' => 'User is not logged in.'];
                }

                // Call 2: Feed back the database rows so Gemini can answer conversationally
                $finalResponse = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->apiKey}", [
                        'contents' => [
                            ['role' => 'user', 'parts' => [['text' => $userMessage]]],
                            ['role' => 'model', 'parts' => [['functionCall' => $firstPart['functionCall']]]],
                            [
                                'role' => 'user',
                                'parts' => [[
                                    'functionResponse' => [
                                        'name' => $functionName,
                                        'response' => ['output' => $dbOutput]
                                    ]
                                ]]
                            ]
                        ],
                        'tools' => $toolsConfig,
                        'systemInstruction' => ['parts' => [['text' => $systemInstruction]]]
                    ]);

                if ($finalResponse->successful()) {
                    $finalResult = $finalResponse->json();
                    $finalText = $finalResult['candidates'][0]['content']['parts'][0]['text'] ?? 'I processed the lookup but could not generate text.';
                    
                    // Package it clean into the expected JSON shape for your frontend js file
                    return response()->json([
                        'status' => 'success',
                        'reply' => [
                            'text' => $finalText,
                            'buttons' => []
                        ]
                    ]);
                }
                
                return response()->json(['status' => 'error', 'reply' => ['text' => 'Failed to resolve data context.', 'buttons' => []]]);
            }

            // Fallback for regular text dialogue without tools
            $fallbackText = $firstPart['text'] ?? 'How can I assist you today?';
            return response()->json([
                'status' => 'success',
                'reply' => [
                    'text' => $fallbackText,
                    'buttons' => []
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'reply' => ['text' => 'Processing error: ' . $e->getMessage(), 'buttons' => []]]);
        }
    }
}