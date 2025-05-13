<?php

namespace App\Jobs;

use App\Events\CsvProcessing;
use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FinalizeCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    public function handle()
    {
        try {
            // Update status to completed
            $this->upload->update(['status' => 'completed']);
            Log::info('Finalized CSV upload ID: ' . $this->upload->id, ['status' => 'completed']);

            // Broadcast the updated status
            event(new CsvProcessing($this->upload, [], $this->upload->user_id));
            Log::info('Broadcasted CsvProcessing event for finalized upload ID: ' . $this->upload->id);
        } catch (\Exception $e) {
            Log::error('Failed to finalize CSV upload ID: ' . $this->upload->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->upload->update(['status' => 'failed']);
            // Broadcast the failed status
            event(new CsvProcessing($this->upload, [], $this->upload->user_id));
            throw $e;
        }
    }
}