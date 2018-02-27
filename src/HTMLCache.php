<?php

namespace JSefton\PageCache;

use Hash;
use Illuminate\Support\Facades\File;

class HTMLCache
{
    /**
     * @var string
     */
    protected $path, $queryString, $hash;

    /**
     * @var string
     */
    protected $storage_folder = "/page_cache";

    /**
     * @var string
     */
    protected $storage_path;

    /**
     * @var string
     */
    protected $contents;

    /**
     * @var
     */
    protected $request;

    /**
     * @var
     */
    protected $next;

    /**
     * Create a new HTML Cache instance.
     *
     * @return void
     */
    public function __construct($request, $next, $path = "", $queryString = "")
    {
        $this->request = $request;
        $this->next = $next;
        $this->path = $path;
        $this->queryString = $queryString;
        $this->generateHash();
        $this->storage_path = storage_path() .  $this->storage_folder;
    }

    public function createCache()
    {
        // If no cache folder then create one
        if(!is_dir($this->storage_path)){
            mkdir($this->storage_path);
        }

        // Disable debugbar
        app('debugbar')->disable();

        $this->getResponse();
        $contents = $this->contents;

        // Minify HTML if enabled.
        // Note: if you change this setting you have to clear the entire cache.
        if(config('pagecache.minify')) {
            $contents = $this->minifyHTML($contents);
        }
        file_put_contents($this->storage_path . "/" . $this->hash, $contents);
        $this->contents = $contents;
    }

    public function getResponse()
    {
        // Send the already existing request to get the final return content
        $closure = $this->next;
        $response = $closure($this->request);
        $this->contents = $response->getContent();
    }

    /**
     * Method called to standardise inline with Response methods
     */
    public function getContent()
    {
        if (in_array($this->path, config('pagecache.exclude'))) {
            $this->bypassCache();
        }
        $this->checkStorage();
        $this->render();
    }

    /**
     * Render the HTML
     */
    public function render()
    {
        echo $this->contents;
        exit;
    }

    /**
     * Bypass cache and proceed as normal
     */
    public function bypassCache()
    {
        header("X-Page-Cache: false");
        $this->getResponse();
        $this->render();
    }

    /**
     * Generate page hash
     */
    protected function generateHash()
    {
        $hash = str_replace("/","_",$this->path) . "_" . md5($this->path . $this->queryString);
        $this->hash = $hash;
    }

    /**
     * Check Storage
     *
     * Checks if current page has already been cached.
     */
    public function checkStorage()
    {
        $path = $this->storage_path;
        $filename = $this->hash;

        // Check if the page has already been generated
        if(file_exists($path . "/" . $filename)){
            if(strpos($this->queryString,"cache=true") === false){
                $createdAt = date("Y-m-d H:i:s", filemtime($path . "/" . $filename));
                header("X-Page-Cache: true");
                header("X-Page-Cache-File: " . $filename);
                header("X-Page-Cache-Created: " . $createdAt);
                $this->contents = file_get_contents($path . "/" . $filename);
                $this->contents = '<!-- Cached URL: ' . $this->path . ' - File: ' . $filename . ' - Generated: ' . $createdAt . '-->' . $this->contents;
            }
        } else {
            $this->createCache();
        }
    }



    /**
     * Clears all stored cache
     */
    public function clearCache()
    {
        if($list = File::files($this->storage_path)){
            foreach($list as $cache){
                unlink($cache);
            }
        }
    }

    /**
     * Clear specific page from the cache
     * @param $path
     */
    public function clearCacheFile($path)
    {
        $this->path = $path;
        $this->generateHash();
        if(file_exists($this->storage_path . "/" . $this->hash)){
            unlink($this->storage_path . "/" . $this->hash);
        }
    }

    /**
     * Minify the response content
     *
     * Parsing from below link
     * @link http://laravel-tricks.com/tricks/minify-html-output
     *
     * @param $buffer
     * @return mixed
     */
    public function minifyHTML($html){
        $buffer = $html;
        if(strpos($buffer,'<pre>') !== false)
        {
            $replace = array(
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/"                  => '<?php ',
                "/\r/"                      => '',
                "/>\n</"                    => '><',
                "/>\s+\n</"                 => '><',
                "/>\n\s+</"                 => '><',
            );
        }
        else
        {
            $replace = array(
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/"                  => '<?php ',
                "/\n([\S])/"                => '$1',
                "/\r/"                      => '',
                "/\n/"                      => '',
                "/\t/"                      => '',
                "/ +/"                      => ' ',
            );
        }
        $buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);
        return $buffer;
    }
}
