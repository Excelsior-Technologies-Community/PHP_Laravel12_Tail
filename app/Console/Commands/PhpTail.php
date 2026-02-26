<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PhpTail extends Command
{
    protected $signature = 'php:tail
                            {lines=10 : Number of lines to show}
                            {--file= : Specify a log file (default: laravel.log)}
                            {--grep= : Filter lines containing a keyword}
                            {--clear : Clear screen before output}
                            {--follow : Continuously tail the log}';

    protected $description = 'Windows-compatible tail for Laravel logs';

    public function handle()
    {
        // Path to the log file
        $file = $this->option('file') ?? storage_path('logs/laravel.log');

        if (!file_exists($file)) {
            $this->error("Log file not found: $file");
            return 1;
        }

        $lines = (int) $this->argument('lines');

        do {
            if ($this->option('clear')) {
                // Clear the console
                echo chr(27)."[H".chr(27)."[2J";
            }

            $content = file($file);

            // Filter lines by keyword if provided
            if ($keyword = $this->option('grep')) {
                $content = array_filter($content, fn($line) => stripos($line, $keyword) !== false);
            }

            // Take last $lines lines
            $tail = array_slice($content, -$lines);

            // Print lines
            foreach ($tail as $line) {
                if ($keyword && stripos($line, $keyword) !== false) {
                    // Highlight keyword in red
                    $highlighted = str_ireplace($keyword, "\033[31m$keyword\033[0m", $line);
                    $this->line($highlighted);
                } else {
                    $this->line($line);
                }
            }

            if ($this->option('follow')) {
                sleep(1);
                clearstatcache();
            }

        } while ($this->option('follow'));

        return 0;
    }
}