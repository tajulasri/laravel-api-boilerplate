<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class GetModelAttributeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:model:attributes {model} {--attributes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get model attributes.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modelClass = $this->getModelNamespace() . $this->argument('model');

        if (!class_exists($modelClass)) {

            $this->error(sprintf('Model class not exists.', $modelClass));
        }

        $schema = [];

        $attributes = Schema::getColumnListing((new $modelClass)->getTable());
        $variable = lcfirst(class_basename($modelClass));

        if (!$this->option('attributes')) {

            foreach ($attributes as $attribute) {
                $schema[] = "'" . $attribute . "'" . ' => ' . '$' . $variable . '->' . $attribute . ',';
            }

            return print implode(PHP_EOL, $schema);
        }

        return $this->info(json_encode($attributes, JSON_PRETTY_PRINT));
    }
}
