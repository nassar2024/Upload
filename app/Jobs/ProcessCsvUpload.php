<?php

namespace App\Jobs;

use App\Events\CsvProcessing;
use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Jobs\FinalizeCsvUpload;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $upload;
    protected $path;
    protected $hash;
    protected $userId;

    public function __construct(Upload $upload, string $path, string $hash, int $userId)
    {
        $this->upload = $upload;
        $this->path = $path;
        $this->hash = $hash;
        $this->userId = $userId;
    }

    public function handle()
    {
        try {
            // Read CSV file
            $file = Storage::disk('public')->path($this->path);
            $handle = fopen($file, 'r');
            if (!$handle) {
                throw new \Exception('Failed to open CSV file: ' . $file);
            }

            // Get header
            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                throw new \Exception('Invalid CSV format: missing header');
            }

            // Update status to processing
            $this->upload->update(['status' => 'processing']);
            Log::info('Started processing CSV upload ID: ' . $this->upload->id);

            // Process CSV in chunks
            $chunkSize = 1000; // Process 1,000 rows at a time
            $data = [];
            $rowCount = 0;
            $totalRows = 0;

            while (($row = fgetcsv($handle)) !== false) {
                $data[] = array_combine($header, $row);
                $rowCount++;
                $totalRows++;

                if ($rowCount >= $chunkSize) {
                    // Dispatch event for this chunk
                    event(new CsvProcessing($this->upload, $data, $this->userId));
                    Log::info('Dispatched CsvProcessing event for upload ID: ' . $this->upload->id, [
                        'rows' => count($data),
                        'total_processed' => $totalRows,
                    ]);
                    $data = []; // Clear the chunk
                    $rowCount = 0;
                }
            }

            // Process any remaining rows
            if (!empty($data)) {
                event(new CsvProcessing($this->upload, $data, $this->userId));
                Log::info('Dispatched CsvProcessing event for upload ID: ' . $this->upload->id, [
                    'rows' => count($data),
                    'total_processed' => $totalRows,
                ]);
            }

            fclose($handle);

            // Log total rows processed
            Log::info('Finished reading CSV upload ID: ' . $this->upload->id, ['total_rows' => $totalRows]);

            // Dispatch job to finalize the upload
            FinalizeCsvUpload::dispatch($this->upload);
        } catch (\Exception $e) {
            Log::error('Failed to process CSV upload: ' . $this->upload->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->upload->update(['status' => 'failed']);
            throw $e;
        }
    }
}