<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH . 'third_party/aws/aws-autoloader.php';

use Aws\S3\S3Client;

class Aws3
{
    public $CI;
    private $s3;
    private $region;
    private $bucket;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model("course_model");
        $settings = $this->CI->course_model->getAwsS3Settings();
        if(!empty($settings)){
            try{
                $this->s3 = new S3Client([
                    'version'     => 'latest',
                    'region'      => $settings->region,
                    'credentials' => [
                        'key'    => $settings->api_key,
                        'secret' => $settings->api_secret,
                    ],
                ]);
            }catch(Exception $e){

            }
          $this->bucket = $settings->bucket_name;
        }
    }

    /**
    * This function is used to upload file to s3 bucket
    */
    public function uploadFile($file_name, $temp_file_location)
    {
        $result = $this->s3->putObject([
            'Bucket'     => $this->bucket,
            'Key'        => $file_name,
            'SourceFile' => $temp_file_location,
        ]);

    }

    /**
    * This function is used to generate a presigned url for the object in s3 bucket
    */
    public function generateUrl($file_name)
    {
        $cmd = $this->s3->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key'    => $file_name,
        ]);
        $request = $this->s3->createPresignedRequest($cmd, '+30 minutes');
        $presignedUrl = (string) $request->getUri();
        return $presignedUrl;
    }
}
