<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Pgvector\Laravel\Distance; // Jangan lupa import Distance

class SearchController extends Controller
{
    /**
     * Menangani permintaan pencarian dengan strategi Hibrida.
     */
    public function handleSearch(Request $request)
    {
        $query = $request->input('query');
        $destinations = null;

        if ($query) {
            // =========================================================
            // JIKA ADA QUERY, LAKUKAN PENCARIAN HIBRIDA
            // (Logika ini dipindahkan dari SearchController)
            // =========================================================
            Log::info("Memulai pencarian keyword untuk: '{$query}'");

            $keywordResults = Destination::where(function ($builder) use ($query) {
                $lowerQuery = strtolower($query);
                $builder->whereRaw('LOWER(title) LIKE ?', ["%{$lowerQuery}%"])
                    // PERBAIKAN: Tambahkan tanda kutip ganda di sekitar "desc"
                    ->orWhereRaw('LOWER("desc") LIKE ?', ["%{$lowerQuery}%"]);
            })
                ->get();

            $semanticResults = collect();

            if ($keywordResults->count() < 5) {
                try {
                    Log::info("Hasil keyword sedikit, memulai pencarian semantik untuk: '{$query}'");
                    $embeddingVector = $this->getEmbeddingForQuery($query);
                    $semanticResults = Destination::query()
                        ->nearestNeighbors('embedding', $embeddingVector, Distance::Cosine)
                        ->take(10)
                        ->get();
                    Log::info("Pencarian semantik berhasil.");
                } catch (\Exception $e) {
                    Log::error("Gagal melakukan pencarian semantik: " . $e->getMessage());
                }
            }

            // Gabungkan hasil dan hapus duplikat
            $destinations = $keywordResults
                ->merge($semanticResults)
                ->unique('id');

        } else {
            // =========================================================
            // JIKA TIDAK ADA QUERY, TAMPILKAN SEMUA DESTINASI
            // =========================================================
            $destinations = Destination::paginate(9);
        }

        // Kembalikan view dengan data yang sesuai
        return view('destination', [
            'destinations' => $destinations,
            'query' => $query // Kirim query ke view untuk ditampilkan di input
        ]);
    }
    /**
     * Fungsi helper untuk memanggil service embedding.
     */
    private function getEmbeddingForQuery(string $query): array
    {
        $embeddingServiceUrl = 'http://127.0.0.1:8002/generate-embedding';
        $response = Http::timeout(10)->post($embeddingServiceUrl, ['text' => $query]);

        if (!$response->successful() || $response->json('status') !== 'success') {
            throw new \Exception('Service embedding tidak merespons dengan benar.');
        }

        return $response->json('embedding');
    }
}
