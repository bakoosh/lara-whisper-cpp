<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class AudioController extends Controller
{
    public function transcribe(Request $request)
    {
        if ($request->file('audio')) {
            $path = $request->file('audio')->store('temp', 'local');
            $fullPath = storage_path('app/' . $path);

            Log::info('Stored file path: ' . $fullPath);

            $process = new Process(['whisper', $fullPath, '--model', 'tiny', '--output_txt']);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('Transcription process failed: ' . $process->getErrorOutput());
                return response()->json(['error' => $process->getErrorOutput()], 500);
            }

            $outputFile = $fullPath . '.txt';
            if (file_exists($outputFile)) {
                $output = file_get_contents($outputFile);
                unlink($outputFile);
            } else {
                $output = $process->getOutput();
            }

            Log::info('Transcription output: ' . $output);

            return response()->json(['transcription' => $output]);
        }

        return response()->json(['error' => 'Invalid file'], 400);
    }
}
