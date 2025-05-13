<?php

namespace App\Listeners;

use App\Events\CsvProcessing;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessCsvData
{
    public function handle(CsvProcessing $event)
    {
        try {
            DB::transaction(function () use ($event) {
                Log::info('Processing CSV data for upload ID: ' . $event->upload->id, ['rows' => count($event->data)]);

                $processedRows = 0;
                $uniqueKeys = []; // Track unique_keys in this chunk to detect duplicates

                foreach ($event->data as $row) {
                    // Clean non-UTF-8 characters
                    $cleanedRow = array_map(function ($value) {
                        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }, $row);

                    // Validate required fields
                    if (empty($cleanedRow['UNIQUE_KEY']) || empty($cleanedRow['PRODUCT_TITLE']) || empty($cleanedRow['PIECE_PRICE'])) {
                        Log::warning('Skipping invalid row in upload ID: ' . $event->upload->id, ['row' => $cleanedRow]);
                        continue;
                    }

                    $uniqueKey = $cleanedRow['UNIQUE_KEY'];
                    if (in_array($uniqueKey, $uniqueKeys)) {
                        Log::warning('Skipping duplicate unique_key in CSV for upload ID: ' . $event->upload->id, [
                            'unique_key' => $uniqueKey,
                            'user_id' => $event->userId,
                            'row' => $cleanedRow,
                        ]);
                        continue;
                    }
                    $uniqueKeys[] = $uniqueKey;

                    // Upsert product
                    Product::updateOrCreate(
                        [
                            'unique_key' => $uniqueKey,
                            'user_id' => $event->userId,
                        ],
                        [
                            'product_title' => $cleanedRow['PRODUCT_TITLE'],
                            'product_description' => $cleanedRow['PRODUCT_DESCRIPTION'] ?? null,
                            'style' => $cleanedRow['STYLE#'] ?? null,
                            'sanmar_mainframe_color' => $cleanedRow['SANMAR_MAINFRAME_COLOR'] ?? null,
                            'size' => $cleanedRow['SIZE'] ?? null,
                            'color_name' => $cleanedRow['COLOR_NAME'] ?? null,
                            'piece_price' => (float) $cleanedRow['PIECE_PRICE'],
                            'user_id' => $event->userId,
                        ]
                    );
                    $processedRows++;
                }

                Log::info('Processed rows for upload ID: ' . $event->upload->id, ['processed' => $processedRows]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to process CSV for upload ID: ' . $event->upload->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $event->upload->update(['status' => 'failed']);
            throw $e;
        }
    }
}