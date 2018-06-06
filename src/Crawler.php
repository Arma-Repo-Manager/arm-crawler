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
 use Exception;

 class Crawler {

    /**
     * Type what the crawler has to crawl
     *
     * @var string
     */
    protected $type;

    /**
     * A list of mods
     * 
     * ['myModTeam' => 'myMod']
     *
     * @var array
     */
    protected $mods;

    /**
     * The github client
     *
     * @var \Github\Client
     */
    protected $client;

    /**
     * The uploader object
     *
     * @var \Arm\Upload
     */
    protected $uploader;

    /**
     * __constructor
     *
     * @param string $type
     * @param array $mods
     * @param array $awsCred
     */
    public function __construct(string $type = 'github', $mods = [], $awsCred = [])
    {
        $this->type = $type;
        $this->mods = $mods;
        $this->client = new Git();
        $this->uploader = new Upload($awsCred[0], $awsCred[1]);
    }

    /**
     * This will start the crawler
     *
     * @return void
     */
    public function run()
    {
        if($this->type === 'github') {
            $this->crawlGithub();
        }
    }

    /** 
     * Crawls als mods from github and saves them in the S3 storage
     *
     * @return void
     */
    protected function crawlGithub()
    {
        foreach($this->mods as $modname => $moduser) {
            $release = $this->client->api('repo')->releases()->all($modname, $moduser)[0];
            foreach($release['assets'] as $asset) {
                $resource = fopen(__DIR__.'/../tmp/'.$asset['name'], 'w');
                $client = new Http();
                $client->request('GET', $asset['browser_download_url'], ['sink' => $resource]);
                $result = $this->uploader->upload(__DIR__.'/../tmp/'.$asset['name'], strtolower($modname).'/'.strtolower($asset['name']), $asset['content_type']);
                if(!$result) {
                    throw new Exception('There was a error, when I tried to upload a mod', 1528311905);
                }
                unlink(__DIR__.'/../tmp/'.$asset['name']);
            }
        }
        return true;
    }
 }