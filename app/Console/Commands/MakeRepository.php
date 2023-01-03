<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name} {--model=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Filesystem instance
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

     /**
     * Execute the console command.
     */
    public function handle()
    {
        // $this->createServiceInterfaceClass();
        $this->createRepositoryClass();
    }

    public function createRepositoryClass()
    {
        if($this->option('model') != 'default' && !$this->files->exists($this->getModelFilePath())) {
            $this->error("Model [".$this->option('model')."] not exist.");
        } else {
            $path = $this->getSourceFilePath();

            $this->makeDirectory(dirname($path));

            $contents = $this->getSourceFile();

            if (!$this->files->exists($path)) {
                $this->files->put($path, $contents);
                $this->info("Service [".$this->argument('name')."] created successfully.");
            } else {
                $this->error("Service [".$this->argument('name')."] already exist.");
            }
        }
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath()
    {
        if($this->option('model') != 'default') {
            return __DIR__ . '/../../../stubs/repository.model.stub';
        }
        return __DIR__ . '/../../../stubs/repository.plain.stub';
    }

    /**
    **
    * Map the stub variables present in stub to its value
    *
    * @return array
    *
    */
    public function getStubVariables()
    {
        $file_name = $this->argument('name');
        $file_name_explode = explode('/', $file_name);
        $namespace_array = array_slice($file_name_explode, 0, count($file_name_explode)-1);
        $namespace_implode = implode("\\", $namespace_array);
        $namespace = count($file_name_explode) == 1 ? $this->getDefaultNamespace() : $this->getDefaultNamespace()."\\${namespace_implode}";

        if($this->option('model') != 'default') {
            return [
                'NAMESPACE'         => $namespace,
                'CLASS_NAME'        => $this->getSingularClassName(end($file_name_explode)),
                'MODEL'             => $this->option('model'),
            ];
        }

        return [
            'NAMESPACE'         => $namespace,
            'CLASS_NAME'        => $this->getSingularClassName(end($file_name_explode)),
        ];
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     *
     */
    public function getSourceFile()
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }


    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param $stub
     * @param array $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContents($stub , $stubVariables = [])
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace)
        {
            $contents = str_replace('$'.$search.'$' , $replace, $contents);
        }

        return $contents;

    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath()
    {
        return base_path($this->getDefaultNamespace()) .'\\' .$this->getSingularClassName($this->argument('name')) . '.php';
    }

    public function getModelFilePath()
    {
        return base_path('App\\Models\\' .$this->getSingularClassName($this->option('model'))) . '.php';
    }

    /**
     * Return the Singular Capitalize Name
     * @param $name
     * @return string
     */
    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace()
    {
        return 'App\\Infrastructures\\Repositories';
    }
}
