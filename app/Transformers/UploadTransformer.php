<?php

namespace App\Transformers;

use App\Models\Upload;
use League\Fractal\TransformerAbstract;

class UploadTransformer extends TransformerAbstract
{
    public function transform(Upload $upload)
    {
        return [
            'id' => $upload->id,
            'filename' => $upload->filename,
            'status' => $upload->status,
            'created_at' => $upload->created_at->toDateTimeString(),
        ];
    }
}