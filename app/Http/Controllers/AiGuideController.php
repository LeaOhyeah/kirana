<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Destination; // Pastikan namespace Model Anda benar

class AiGuideController extends Controller
{
    public function askGuide(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'destination_id' => 'required|integer',
            'prompt' => 'required|string'
        ]);

        $destinationId = $request->input('destination_id');
        $userPrompt = $request->input('prompt');

        // 2. Ambil Data dari Database
        $destination = Destination::find($destinationId);

        if (!$destination) {
            Log::warning("⚠️ Destination ID {$destinationId} not found.");
            return response()->json(['status' => 'error', 'message' => 'Destination not found'], 404);
        }

        // 3. Susun Payload untuk Service Python
        // Python mengharapkan struktur: { prompt, context: { title, description } }
        $pythonPayload = [
            'prompt' => $userPrompt,
            'context' => [
                'title' => $destination->title,
                // Mengambil dari kolom 'data_detail' seperti permintaan Anda
                // Jika data_detail kosong, bisa fallback ke 'description' atau string kosong
                'description' => $destination->data_detail ?? $destination->description ?? 'No details available.'
            ]
        ];

        // 4. Logging Request
        Log::info('🤖 AI REQUEST START', [
            'user_ip' => $request->ip(),
            'destination_id' => $destinationId,
            'title' => $destination->title,
            'data_detail' => $destination->data_detail,
            'prompt_snippet' => substr($userPrompt, 0, 50)
        ]);

        try {
            // 5. Kirim ke Python Service
            $response = Http::timeout(30)->post('http://127.0.0.1:8001/voice-process', $pythonPayload);

            // 6. Return Response ke Frontend
            if ($response->successful()) {
                Log::info('✅ AI RESPONSE SUCCESS');
                return $response->json();
            } else {
                Log::error('❌ AI SERVICE ERROR: ' . $response->body());
                return response()->json(['status' => 'error', 'message' => 'AI Service error'], 500);
            }

        } catch (\Exception $e) {
            Log::error('🔥 LARAVEL CONNECTION ERROR: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server connection failed'], 500);
        }
    }
}