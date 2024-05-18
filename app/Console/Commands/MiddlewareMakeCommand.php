<?php
/**
 * Author david you
 * Date 2024/5/17
 * Time 14:41
 */
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MiddlewareMakeCommand extends GeneratorCommand {
    /**
     * create a user defined middleware.
     *
     * @var string
     */
    protected $name = 'make:middleware';  //要添加的命令

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new lumen middleware '; //命令描述

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Middleware';  // command type

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() {
        return dirname(__DIR__) . '/Commands/stubs/middleware.stub';  //要生成的文件的模板
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace) {
        return $rootNamespace . '\Http\Middleware';//这里是定义要生成的类的命名空间
    }
}
