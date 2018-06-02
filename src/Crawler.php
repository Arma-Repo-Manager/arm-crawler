<?php
/**
 * Main class of the crawler package.
 * Do not fuck up here, it can break a lot
 * 
 * @author flaver<zerbarian@outlook.com>
 * @copyright (c)2018 ARM, flaver
 * @package arm.crawler
 */

 namespace Arm\Crawler;

 use Github\Client as Git;
 use Goutte\Client;

 class Crawler {

    protected $type;

    protected $mods;

    protected $client;

    public function __construct(string $type = 'github', $mods = [])
    {
        $this->type = $type;
        $this->mods = $mods;
        $this->client = new Git();
    }

    public function run()
    {
        if($this->type === 'github') {
            $this->crawlGithub();
        }
    }

    protected function crawlGithub()
    {
        foreach($this->mods as $modname => $moduser) {
            $release = $this->client->api('repo')->releases()->all($modname, $moduser)[0];
            echo "<pre>";
            var_dump($release);
        }
    }
 }