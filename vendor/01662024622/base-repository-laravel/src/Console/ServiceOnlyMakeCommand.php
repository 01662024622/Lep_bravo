<?php
namespace Repository\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ServiceOnlyMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:serviceOnly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new only class for Service class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'ServiceClass';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (parent::handle() === false && !$this->option('force')) {
            return;
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/service.stub';
    }

    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        // create interface infor
        $interface=str_replace('Impl', '', $class);
        $namespace=str_replace('Impl', '', $this->argument('name'));

        // create repository infor
        $repository=str_replace('ServiceImpl', '', $class)."Repository";
        $repositoryNamespace=str_replace('ServiceImpl', '', $this->argument('name'))."Repository";

        // add interface
        $stub=str_replace(['DummyInterface','{{ interface }}','{{interface}}'], $interface, $stub);
        $stub=str_replace(['DummyNamespaceInterface','{{ NamespaceInterface }}','{{NamespaceInterface}}'], $namespace, $stub);

        // add repository
        $stub=str_replace(['DummyRepository','{{ repository }}','{{repository}}'], $repository, $stub);
        $stub=str_replace(['DummyNamespaceRepository','{{ NamespaceRepository }}','{{NamespaceRepository}}'], $repositoryNamespace, $stub);
        return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
    }
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Services\Impl';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the service already exists.'],
        ];
    }
}