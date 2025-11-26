<?php

namespace App\Console\Commands;

use App\Models\ClientSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DebugSessionFiles extends Command
{
    protected $signature = 'debug:session-files';
    protected $description = 'Debug client session files';

    public function handle()
    {
        $sessions = ClientSession::whereNotNull('medical_record_path')->get();

        foreach ($sessions as $session) {
            $this->line("Session ID: {$session->id}");
            $this->line("  Raw Value: " . json_encode($session->medical_record_path));
            $this->line("  Type: " . gettype($session->medical_record_path));

            if (is_array($session->medical_record_path)) {
                foreach ($session->medical_record_path as $file) {
                    $exists = Storage::disk('public')->exists($file);
                    $this->line("    - $file: " . ($exists ? '✓ EXISTS' : '✗ MISSING'));
                }
            }
            $this->line("");
        }
    }
}
