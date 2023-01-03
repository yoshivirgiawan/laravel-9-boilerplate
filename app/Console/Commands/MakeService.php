<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';

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
        $this->createServiceClass();
    }

    public function createServiceInterfaceClass()
    {
        $path = $this->getInterfaceSourceFilePath();

        $this->makeDirectory(dirname($path));

        $contents = $this->getInterfaceSourceFile();

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("Service Interface [".$this->argument('name')."Interface] created successfully.");
        } else {
            $this->info("Service Interface [".$this->argument('name')."Interface] already exist.");
        }
    }

    public function createServiceClass()
    {
        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile();

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("Service [".$this->argument('name')."] created successfully.");
        } else {
            $this->info("Service [".$this->argument('name')."] already exist.");
        }
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath()
    {
        return __DIR__ . '/../../../stubs/service.plain.stub';
    }

    public function getInterfaceStubPath()
    {
        return __DIR__ . '/../../../stubs/service.interface.stub';
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

        return [
            'NAMESPACE'         => $namespace,
            'CLASS_NAME'        => $this->getSingularClassName(end($file_name_explode)),
        ];
    }

    public function getInterfaceStubVariables()
    {
        $file_name = $this->argument('name').'Interface';
        $file_name_explode = explode('/', $file_name);
        $namespace_array = array_slice($file_name_explode, 0, count($file_name_explode)-1);
        $namespace_implode = implode("\\", $namespace_array);
        $namespace = count($file_name_explode) == 1 ? $this->getInterfaceNamespace() : $this->getInterfaceNamespace()."\\${namespace_implode}";

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

    public function getInterfaceSourceFile()
    {
        return $this->getStubContents($this->getInterfaceStubPath(), $this->getInterfaceStubVariables());
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

    public function getInterfaceSourceFilePath()
    {
        return base_path($this->getDefaultNamespace()) .'\\Interfaces\\' .$this->getSingularClassName($this->argument('name')) . 'Interface.php';
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
        return 'App\\Infrastructures\\Services';
    }

    protected function getInterfaceNamespace()
    {
        return $this->getDefaultNamespace().'\\Interfaces';
    }

    // protected function getBasePath()
    // {
    //     return base_path('app');
    // }

    // /**
    //  * Execute the console command.
    //  *
    //  * @return int
    //  */
    // public function handle()
    // {
    //     $file_name = $this->argument('file_name');
    //     $file_name_explode = explode('/', $file_name);
    //     $namespace_array = array_slice($file_name_explode, 0, count($file_name_explode)-1);
    //     $namespace_implode = implode("\\", $namespace_array);
    //     $namespace = count($file_name_explode) == 1 ? $this->getDefaultNamespace() : $this->getDefaultNamespace()."\\${namespace_implode}";
    //     $class_name = end($file_name_explode);

    //     $fileContents = <<<EOT
    //     <?php

    //     namespace ${namespace};

    //     class ${class_name}
    //     {
    //         // Your Code Here
    //     }

    //     EOT;

    //     $path = 'Services/'.$file_name.'.php';

    //     $file_exist = Storage::disk('app')->exists($path);

    //     if(!$file_exist) {
    //         $written = Storage::disk('app')->put($path, $fileContents);

    //         if($written) {
    //             $this->info("INFO: Service [${file_name}] created successfully.");
    //             return Command::SUCCESS;
    //         } else {
    //             $this->error('ERROR: Something went wrong');
    //             return Command::FAILURE;
    //         }
    //     } else {
    //         $this->error("ERROR: Service [${file_name}] already exist.");
    //         return Command::FAILURE;
    //     }
    // }
}
