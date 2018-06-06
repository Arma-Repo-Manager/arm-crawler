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

class Upload {

    protected $s3Client;

    public function __construct($awsAccessKeyId, $awsSecretAccessKey)
    {
        $awsCredentials = new Credentials($awsAccessKeyId, $awsSecretAccessKey);
        $this->s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => 'eu-central-1',
            'credentials' => $awsCredentials
        ]);
    }

    public function upload($file, $dest, $fileType)
    {
        $uploader = new MultipartUploader($this->s3Client, $file, [
            'bucket' => 'arm-storage-1',
            'key'    => 'mods/'.$dest,
        ]);
        
        try {
            $result = $uploader->upload();
            //echo "Upload complete: {$result['ObjectURL']}\n";
        } catch (MultipartUploadException $e) {
            echo $e->getMessage() . "\n";
            return false;
        }
        return true;
    }
}