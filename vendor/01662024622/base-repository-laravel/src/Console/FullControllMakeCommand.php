<?php
namespace Repository\Console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class FullControllMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:fullControll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new full controll';

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

        $this->createRepositoryClass();

    }

    protected function createRepositoryClass()
    {
        $this->call('make:repository', [
            'name' => "{$this->argument('name')}Repository",
        ]);
        $this->call('make:service', [
            'name' => "{$this->argument('name')}Service",
        ]);
        $this->call('make:model', [
            'name' => "Models\\{$this->argument('name')}",
        ]);
        $this->call('make:controller', [
            'name' => "{$this->argument('name')}Controller",
        ]);
    }

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/repositoryInterface.stub';
    }


    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the repository already exists.'],
        ];
    }
}