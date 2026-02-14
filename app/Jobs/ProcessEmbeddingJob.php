<?php

namespace App\Jobs;

use App\Models\Destination;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessEmbeddingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $destinationId;

    /**
     * Berapa kali job boleh dicoba sebelum dianggap gagal.
     * @var int
     */
    public $tries = 5;

    public $backoff = [60, 60, 60, 60, 60]; // Coba lagi setelah 1 menit, lalu 2 menit

    /**
     * Create a new job instance.
     */
    public function __construct(Destination|int $destination)
    {
        // Jika yang dikirim adalah objek Model, ambil ID-nya.
        // Jika yang dikirim sudah berupa ID, langsung gunakan.
        if ($destination instanceof Destination) {
            $this->destinationId = $destination->id;
        } else {
            $this->destinationId = $destination;
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Ambil kembali data destinasi TERBARU dari database
        $destination = Destination::find($this->destinationId);

        // Jika karena suatu alasan data sudah dihapus, hentikan job
        if (!$destination) {
            Log::warning("Job embedding dibatalkan karena destinasi ID: {$this->destinationId} tidak ditemukan.");
            return;
        }

        // Siapkan teks dari data yang baru kita ambil
        $textToEmbed = $destination->title . ". " . $destination->desc;

        $embeddingServiceUrl = 'http://127.0.0.1:8002/generate-embedding';

        try {
            Log::info("Memulai job embedding untuk destinasi ID: {$destination->id}");

            $response = Http::timeout(60)->post($embeddingServiceUrl, ['text' => $textToEmbed]);

            if ($response->successful() && $response->json('status') === 'success') {
                $embedding = $response->json('embedding');
                $embeddingString = '[' . implode(',', $embedding) . ']';

                // Gunakan objek $destination untuk update, atau DB::table seperti sebelumnya
                $destination->update(['embedding' => $embeddingString]);

                // $embeddingVector = $response->json('embedding');
                // $destination->embedding = $embeddingVector;
                // $destination->save();

                Log::info("Sukses meng-update embedding untuk destinasi ID: {$destination->id}");
            } else {
                Log::error("Service embedding gagal untuk destinasi ID: {$destination->id}. Status: " . $response->status());
                $this->release(120);
            }
        } catch (\Exception $e) {
            Log::error("Gagal menghubungi service embedding untuk destinasi ID: {$destination->id}. Error: " . $e->getMessage());
            $this->release(120);
        }
    }
}
