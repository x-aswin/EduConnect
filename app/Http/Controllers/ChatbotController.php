<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function message(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $service = new ChatbotService();
        $response = $service->process($request->message);

        return response()->json($response);
    }
}