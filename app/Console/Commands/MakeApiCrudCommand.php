<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class MakeApiCrudCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'api:crud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate crud controller for API.';

    /**
     * type being generated.
     * @var string
     */
    protected $type = 'Controller';

    /**
     * namespace models
     * @var string
     */
    protected $modelNamespace = 'Entity\\';

    /**
     * transformer namespace
     * @var string
     */
    protected $transformerNamespace = 'Http\\Transformers\\';

    /**
     * controller namespace
     * @var string
     */
    protected $controllerNamespace = 'Http\\Controllers\\Api';
    /**
     * get stub
     * @return [type] [description]
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/controller.crud.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $namespace = $rootNamespace . '\\' . $this->controllerNamespace;
        return $this->specifyVersion() ? $namespace . '\\' . $this->option('api-version') : $namespace;
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

        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('model')) {
            //should be mandatory
            $modelClass = $this->parseModel($this->option('model'));
            $transformerClass = $this->parseTransformer($this->option('transformer'));

            if (!class_exists($modelClass)) {
                if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                    $this->call('make:model', ['name' => $modelClass]);
                }
            }

            if (!class_exists($transformerClass)) {

                if ($this->confirm("A {$transformerClass} model does not exist. Do you want to generate it?", true)) {
                    $this->call('make:transformer', ['name' => $this->option('model') . 'Transformer', 'model' => $this->option('model')]);
                }

            }

            $replace = [
                'DummyFullModelTransformer' => $transformerClass,
                'DummyTransformer'          => class_basename($transformerClass),
                'DummyFullModelClass'       => $modelClass,
                'DummyModelClass'           => class_basename($modelClass),
                'DummyModelVariable'        => lcfirst(class_basename($modelClass)),
                'DummyTag'                  => lcfirst(class_basename($modelClass)),
                //just use lower string for the moment.
                'DummyPath'                 => Str::lower(class_basename($modelClass)),
            ];
        }

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
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
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     */
    protected function parseTransformer($transformer)
    {
        $transformer = trim(str_replace('/', '\\', $transformer), '\\');
        $qualifiedNamespace = $this->laravel->getNamespace() . $this->transformerNamespace . $transformer;
        return $qualifiedNamespace;
    }

    /**
     * specify version for api generated.
     * @return [type] [description]
     */
    protected function specifyVersion()
    {
        return $this->option('api-version') ?: false;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'Generate a resource controller for the given model.'],
            ['transformer', 't', InputOption::VALUE_REQUIRED, 'Use respective transformer for generated controller.'],
            ['api-version', 'api-ver', InputOption::VALUE_OPTIONAL, 'Set api version for controller path.'],
        ];
    }
}
