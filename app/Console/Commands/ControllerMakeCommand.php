<?php
/**
 * Author david you
 * Date 2024/5/17
 * Time 14:34
 */
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class ControllerMakeCommand extends GeneratorCommand {
    /**
     * create a user defined controller.
     *
     * @var string
     */
    protected $name = 'make:controller';  //要添加的命令

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new lumen controller '; //命令描述

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';  // command type

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() {
        return dirname(__DIR__) . '/Commands/stubs/controller.stub';  //要生成的文件的模板
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace) {
        return $rootNamespace . '\Http\Controllers';//这里是定义要生成的类的命名空间
    }
}
