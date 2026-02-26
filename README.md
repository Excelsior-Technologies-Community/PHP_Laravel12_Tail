# PHP_Laravel12_Tail


## Project Description

PHP_Laravel12_Tail is a Windows-compatible log tailing tool for Laravel 12.

It allows you to view, filter, and follow Laravel log files directly in Windows CMD or PowerShell, without needing Linux commands like tail.

You can see the latest log entries, search for specific keywords, highlight them, and even follow logs in real-time as your application runs.

This project demonstrates how to create a custom Laravel Artisan command (php:tail) that works on Windows, making debugging and monitoring logs easier during development.



## Key Features

1. Windows-friendly tail command – No Linux dependencies required.

2. Filter by keyword – Highlight specific words (e.g., “ERROR” or “Home”) in logs.

3. Follow logs in real-time – See new log entries appear live in the terminal.

4. Custom log files – Tail any log file, including daily logs.

5. Clear console support – Clean and organized log output for better readability.



## Technologies

1. PHP 8.2 – Backend language

2. Laravel 12 – Framework for web & Artisan commands

3. Windows CMD / PowerShell – Run tail command

4. Composer – Dependency management

5. Tinker – Generate test logs


---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_Tail "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_Tail

```

#### Explanation:

This installs a fresh Laravel 12 project and navigates into the folder.





## STEP 2: Make the Windows-compatible php:tail command

### Create a new command:

```
php artisan make:command PhpTail

```

### Open app/Console/Commands/PhpTail.php and replace the content with this Windows-friendly version:

```
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

```


#### Explanation:

This generates a new Laravel artisan command file inside app/Console/Commands/PhpTail.php.

This command allows you to tail Laravel log files with options for lines, grep, follow, clear screen, and custom log files.






## STEP 3: Make Sure Log File Exists

### Laravel needs a log file to tail. By default:

```
storage/logs/laravel.log

```

- If it doesn’t exist, create it manually.

#### Or generate a log entry using Tinker:

```
php artisan tinker

Log::info("Visited Home Page");

exit;

```


#### Explanation:

The tail command requires the log file to exist, otherwise it will show an error.

This will create the log file automatically with at least one entry.






## STEP 4: Clear caches:

### Run these commands to ensure Laravel recognizes the new command:

```
php artisan config:clear
php artisan cache:clear
composer dump-autoload

```

#### Explanation:

Clearing caches ensures that Laravel loads your new command correctly.




## STEP 5: Run Your Command

### Show last 10 lines (default):

```
php artisan php:tail

```
#### Output:


<img width="1467" height="105" alt="Screenshot 2026-02-26 163847" src="https://github.com/user-attachments/assets/f6a22b5f-2d1e-4333-8e81-f9779e714ba8" />


### Show last 10 lines and follow new logs:

```
php artisan php:tail 10 --follow
```

#### Output:

<img width="1456" height="574" alt="Screenshot 2026-02-26 163903" src="https://github.com/user-attachments/assets/373ef74f-5eea-4723-877d-32dca5fe9705" />


### Test keyword filtering:

#### Show last 10 lines with keyword filter

```
php artisan php:tail 10 --grep="Home"

```

#### Output:


<img width="1439" height="119" alt="Screenshot 2026-02-26 163919" src="https://github.com/user-attachments/assets/0e841431-532c-4da2-8a4e-c58727a7c7ee" />


- “Home” will appear in red.

- This works fully on Windows CMD or PowerShell, no Linux tail needed.



---

# Project Folder Structure: 

```
PHP_Laravel12_Tail/
├── app/
│   └── Console/
│       └── Commands/
│           └── PhpTail.php   <-- Windows tail command
├── routes/
│   └── web.php               <-- Web routes
├── resources/
│   └── views/
│       └── welcome.blade.php <-- Example view
├── storage/
│   └── logs/
│       └── laravel.log       <-- Log file to tail
├── .env                      <-- Environment config
└── artisan                   <-- CLI to run php:tail

```
