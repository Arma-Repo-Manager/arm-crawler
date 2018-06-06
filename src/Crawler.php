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
 use GuzzleHttp\Client as Http;

 class Crawler {

    protected $type;

    protected $mods;

    protected $client;

    protected $uploader;

    public function __construct(string $type = 'github', $mods = [], $awsCred = [])
    {
        $this->type = $type;
        $this->mods = $mods;
        $this->client = new Git();
        $this->uploader = new Upload($awsCred[0], $awsCred[1]);
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
            foreach($release['assets'] as $asset) {
                $resource = fopen(__DIR__.'/../tmp/'.$asset['name'], 'w');
                $client = new Http();
                $client->request('GET', $asset['browser_download_url'], ['sink' => $resource]);
                $result = $this->uploader->upload(__DIR__.'/../tmp/'.$asset['name'], $modname.'/'.$asset['name'], $asset['content_type']);
                if(!$result) {
                    die('error');
                }
                unlink(__DIR__.'/../tmp/'.$asset['name']);
            }
        }
        return true;
    }
 }