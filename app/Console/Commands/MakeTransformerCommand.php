<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeTransformerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:transformer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate transformer';

    /**
     * type to generate
     * @var string
     */
    protected $type = 'Transformer';

    /**
     * namespace models
     * @var string
     */
    protected $modelNamespace = 'Entity\\';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/transformer.plain.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Transformers';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $transformerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('model')) {
            $modelClass = $this->parseModel($this->option('model'));

            if (!class_exists($modelClass)) {

                if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                    $this->call('make:model', ['name' => $modelClass]);
                }
                //throw new FileNotFoundException(sprintf('Model are not found %s', $modelClass));
            }

            $attributes = $this->parseAttributes($modelClass);

            $replace = [
                'DummyFullModelClass' => $modelClass,
                'DummyModelClass'     => class_basename($modelClass),
                'DummyModelVariable'  => lcfirst(class_basename($modelClass)),
                'Attributes'          => $attributes,
            ];
        }

        $replace["use {$transformerNamespace}\Transformer;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * parse attributes from selected model
     * @param  [type] $modelClass [description]
     * @return [type]             [description]
     */
    private function parseAttributes($modelClass)
    {
        $schema = [];

        $attributes = Schema::getColumnListing((new $modelClass)->getTable());
        $variable = lcfirst(class_basename($modelClass));

        foreach ($attributes as $attribute) {
            $schema[] = "'" . $attribute . "'" . ' => ' . '$' . $variable . '->' . $attribute . ',';
        }

        return implode(PHP_EOL, $schema);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (!Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace . $this->modelNamespace . $model;
        }

        return $model;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Generate a transfomer for the given model.'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'Use this models inside transformation.'],
        ];
    }
}
