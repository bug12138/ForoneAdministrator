<?php

namespace Forone\Console;

use Illuminate\Console\Command;

class quick extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forone:quick';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '快速生成对应的model,view,controller文件,你只需要修改路由,修改forone配置,创建表';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->ask('What is your file name');
        $name = studly_case($name);
        try{
            $this->makeModel($name);
            $this->makeController($name);
            $this->makeView($name);
        } catch (\Exception $e){
            $this->error('quick make error');
        }
    }

    private function makeModel($name)
    {
        if($name){
            $file = file_get_contents("vendor/forone/administrator/src/Forone/BaseFile/BaseModel.php");
            $file = str_replace('BaseModel',$name,$file);
            if(file_put_contents("app/Models/{$name}.php",$file)){
                $this->info($name . " 's Model is OK");
            }
        }else {
            $this->error($name."'s Model make false");
        }

    }

    private function makeView($name)
    {
        $name = strtolower($name);
        if($name){
            if (!is_dir("resources/views/{$name}/")) {
                mkdir("resources/views/{$name}/", 0755, true);
            }

            if($this->copy_dir("vendor/forone/administrator/src/Forone/BaseFile/BaseView/" , "resources/views/{$name}/")){
                $this->info($name . " 's View is OK");
            }
        } else {
            $this->error($name."'s View make false");
        }
    }

    private function makeController($name)
    {
        if($name){
            $file = file_get_contents("vendor/forone/administrator/src/Forone/BaseFile/BaseController.php");
            $file = str_replace('BaseText',$name,$file);

            if (!is_dir("app/Http/Controllers/{$name}/")) {
                mkdir("app/Http/Controllers/{$name}/", 0755, true);
            }

            if(file_put_contents("app/Http/Controllers/{$name}/{$name}Controller.php",$file)){
                $this->info($name . " 's Controller is OK");
            }
        } else {
            $this->error($name."'s View make false");
        }
    }

    private function copy_dir($src,$dst) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    copy_dir($src . '/' . $file,$dst . '/' . $file);
                    continue;
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        return true;
    }
}
