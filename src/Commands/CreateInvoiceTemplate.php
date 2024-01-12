<?php

namespace GlennRaya\Xendivel\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateInvoiceTemplate extends Command
{
    protected $signature = 'xendivel:invoice {name}';

    protected $description = 'Create a new Xendivel invoice template.';

    public function handle()
    {
        $name = $this->argument('name');
        $content = File::get(__DIR__.'/../../resources/views/invoice.blade.php');

        $template_path = resource_path('views/vendor/xendivel/');

        if (! is_dir($template_path)) {
            try {
                mkdir($template_path, 0755, true);
            } catch (Exception $e) {
                throw new Exception("Xendivel is unable to create a directory for invoice template in $template_path. Please ensure that you have the necessary write permissions for this location.");
            }
        }

        $directory = $template_path."$name.blade.php";

        if (File::exists($directory)) {
            $this->error('Invoice template already exists!');

            return;
        }

        File::put($directory, $content);

        $this->info("Invoice template created successfully: $name");
    }
}
