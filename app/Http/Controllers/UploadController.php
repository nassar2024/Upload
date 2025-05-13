<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCsvUpload;
use App\Models\Upload;
use App\Transformers\UploadTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class UploadController extends Controller
{
    public function index()
    {
        $uploads = Upload::where('user_id', Auth::id())->get();
        $fractal = new Manager();
        $resource = new Collection($uploads, new UploadTransformer());
        return $fractal->createData($resource)->toArray();
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $hash = md5_file($file->getRealPath());

        // Store file
        $path = $file->storeAs('uploads', $filename, 'public');

        // Create upload record
        $upload = Upload::create([
            'user_id' => Auth::id(),
            'filename' => $filename,
            'status' => 'pending',
        ]);

        // Dispatch job
        ProcessCsvUpload::dispatch($upload, $path, $hash, Auth::id());

        return response()->json(['message' => 'File uploaded and processing started'], 201);
    }
}