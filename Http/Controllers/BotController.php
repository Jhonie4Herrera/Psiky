<?php

namespace App\Http\Controllers;

use App\Services\BotService;
use Illuminate\Http\Request;

class BotController extends Controller
{
    protected $botService;

    public function __construct(BotService $botService)
    {
        $this->botService = $botService;
    }

    public function interact(Request $request)
    {
        $response = $this->botService->processMessage($request->message, auth()->user());
        return response()->json(['response' => $response]);
    }

    public function analyzeEmotions(Request $request)
    {
        $score = $this->botService->analyzeEmotions($request->faceData);
        $upgradePrompt = $this->botService->checkForUpgrade(auth()->user(), $score);
        return response()->json(['score' => $score, 'prompt' => $upgradePrompt]);
    }

    public function switchMode(Request $request)
    {
        session(['bot_mode' => $request->mode]); // Toggle chat/2D, estado en session
        return response()->json(['mode' => $request->mode]);
    }
}