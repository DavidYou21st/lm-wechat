<?php
/**
 * Author david you
 * Date 2024/5/17
 * Time 14:34
 */
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class RequestMakeCommand extends GeneratorCommand {
    /**
     * create a user defined model.
     *
     * @var string
     */
    protected $name = 'make:request';  //要添加的命令

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new lumen request '; //命令描述

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';  // command type

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() {
        return dirname(__DIR__) . '/Commands/stubs/request.stub';  //要生成的文件的模板
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace) {
        return $rootNamespace . '\Http\Requests';//这里是定义要生成的类的命名空间
    }
}
