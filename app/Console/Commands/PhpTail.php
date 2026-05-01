<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PhpTail extends Command
{
    protected $signature = 'php:tail
                            {lines=10 : Number of lines to show}
                            {--file= : Specify a log file (default: laravel.log)}
                            {--grep= : Filter lines containing a keyword}
                            {--level= : Filter logs by level (INFO, ERROR, DEBUG, WARNING)}
                            {--export= : Export logs to a file path}
                            {--clear : Clear screen before output}
                            {--follow : Continuously tail the log}';

    protected $description = 'Windows-compatible tail for Laravel logs';

    public function handle()
    {
        // Path to the log file
        $file = $this->option('file') ?? storage_path('logs/laravel.log');

        // Auto-create file if missing (safe improvement)
        if (!file_exists($file)) {
            if (!is_dir(dirname($file))) {
                mkdir(dirname($file), 0777, true);
            }
            file_put_contents($file, "");
        }

        $lines = (int) $this->argument('lines');

        do {
            if ($this->option('clear')) {
                echo chr(27) . "[H" . chr(27) . "[2J";
            }

            // Read file
            $content = file($file);

            /*
            |--------------------------------------------------------------------------
            | LEVEL FILTER (NEW FEATURE)
            |--------------------------------------------------------------------------
            */
            if ($level = $this->option('level')) {
                $content = array_filter($content, function ($line) use ($level) {
                    return stripos($line, $level) !== false;
                });
            }

            /*
            |--------------------------------------------------------------------------
            | GREP FILTER (EXISTING FEATURE)
            |--------------------------------------------------------------------------
            */
            if ($keyword = $this->option('grep')) {
                $content = array_filter($content, function ($line) use ($keyword) {
                    return stripos($line, $keyword) !== false;
                });
            }

            // Take last N lines
            $tail = array_slice($content, -$lines);

            /*
            |--------------------------------------------------------------------------
            | EXPORT FEATURE (NEW)
            |--------------------------------------------------------------------------
            */
            if ($exportPath = $this->option('export')) {
                file_put_contents($exportPath, implode("", $tail));
                $this->info("Logs exported to: " . $exportPath);
            }

            // Print logs
            foreach ($tail as $line) {

                // Highlight grep keyword in red
                if (!empty($keyword) && stripos($line, $keyword) !== false) {
                    $line = str_ireplace(
                        $keyword,
                        "\033[31m{$keyword}\033[0m",
                        $line
                    );
                }

                // Highlight log levels (bonus UI improvement)
                $line = str_ireplace(
                    ['ERROR', 'WARNING', 'INFO', 'DEBUG'],
                    ["\033[31mERROR\033[0m", "\033[33mWARNING\033[0m", "\033[32mINFO\033[0m", "\033[36mDEBUG\033[0m"],
                    $line
                );

                $this->line($line);
            }

            if ($this->option('follow')) {
                sleep(1);
                clearstatcache();
            }

        } while ($this->option('follow'));

        return 0;
    }
}