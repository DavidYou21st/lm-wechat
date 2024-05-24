<?php
/**
 * Author david you
 * Date 2024/5/17
 * Time 14:34
 */
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class ModelMakeCommand extends GeneratorCommand {
    /**
     * create a user defined model.
     *
     * @var string
     */
    protected $name = 'make:model';  //要添加的命令

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new lumen model '; //命令描述

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';  // command type

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() {
        return dirname(__DIR__) . '/Commands/stubs/model.stub';  //要生成的文件的模板
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace) {
        return $rootNamespace.'\\Models';
    }
}
