<?php

namespace JSefton\PageCache\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearPageCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pagecache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears Page Cache';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if($list = File::files(storage_path() .'/page_cache')){
            foreach($list as $cache){
                unlink($cache);
            }
        }

        $this->info('Page cache cleared!');
    }
}
