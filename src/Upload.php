<?php
/**
 * Uploader for the s3 storage
 * 
 * @author flaver<zerbarian@outlook.com>
 * @copyright (c)2018 ARM, flaver
 * @package arm.crawler
 */

namespace Arm\Crawler;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;
use Exception;

class Upload {

    /**
     * Instance of the s3 client
     *
     * @var \Aws\S3\S3Client
     */
    protected $s3Client;

    /**
     * __construct
     *
     * @param string $awsAccessKeyId
     * @param string $awsSecretAccessKey
     */
    public function __construct($awsAccessKeyId, $awsSecretAccessKey)
    {
        $awsCredentials = new Credentials($awsAccessKeyId, $awsSecretAccessKey);
        $this->s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => 'eu-central-1',
            'credentials' => $awsCredentials
        ]);
    }

    /**
     * Uploads a file to s3
     *
     * @param string $file
     * @param string] $dest
     * @param string $fileType
     * @return void
     */
    public function upload($file, $dest, $fileType)
    {
        $uploader = new MultipartUploader($this->s3Client, $file, [
            'bucket' => 'arm-storage-1',
            'key'    => 'mods/'.$dest,
        ]);
        
        try {
            $result = $uploader->upload();
        } catch (MultipartUploadException $e) {
            throw new Exception($e->getMessage(), 1528312013);
        }
        return true;
    }
}