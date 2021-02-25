<?php
header('Content-type: application/json');
require_once('dbconnection.php');
$response = array();

if($_SERVER['REQUEST_METHOD']=='POST')
{
    $uploadedByUserName = $_POST['uploaded_by_name'];
    $fileNameSendByUser = $_POST['file_name'];
    $file               = $_FILES['file_parameter'];

    if(empty($uploadedByUserName))
    {
        $response['status']     = false;
        $response['message']    = 'Uploaded By Name Field Missing';
        echo json_encode($response);
    } 
    else if(empty($fileNameSendByUser))
    {
        $response['status']     = false;
        $response['message']    = 'File Name Field Missing';
        echo json_encode($response);
    }
    else if(empty($file))
    {
        $response['status']     = false;
        $response['message']    = 'File Field Missing';
        echo json_encode($response);
    }
    else if($connection)
    {
        /*
        The uploaded file is stored in the $_FILES['file'] array, where 'file' is the name parameter. The other contents of the $_FILES['file'] array are :
        Array
        (
            [name] => Screenshot_20200124-112920_Tutti Artist.jpg
            [type] => image/*
            [tmp_name] => /tmp/php0gmwgM
            [error] => 0
            [size] => 215428
        )
        
        $_FILES['file']['name'] -- original name of the file on the client machine.
        $_FILES['file']['type'] -- mime type of the file, provided by the browser
        $_FILES['file']['tmp_name'] -- temporary filename of the uploaded file, stored on the server, contains the actual copy of your file content on the server while
        $_FILES['file']['error'] -- returns an error code, useful for debugging.
        $_FILES['file']['size'] -- size (in bytes) of the uploaded file
        */
        
        $fileName           = $_FILES['file_parameter']['name'];
        $fileContentType    = $_FILES['file_parameter']['type'];
        $fileTempName       = $_FILES['file_parameter']['tmp_name'];
        $fileError          = $_FILES['file_parameter']['error'];
        $fileSize           = $_FILES['file_parameter']['size'];
        
        /*
        Allow extensions
        */
        $fileExtensions = ['jpeg','jpg','png','gif', 'pdf', 'exe', 'ooooooo', '7z', 'vcf', 'csv', 'mp4'];
        
        $value = explode(".", $fileName);
        $fileExtension = strtolower(end($value));

        /*
        Folder name where file store, in my case folder name is uploaded_files
        */
        $folderName = "uploaded_files/";
        
        /*
        Base url where uploaded_files folder created
        */
        $baseUrl    = "http://bwsproduction.com/api.com/UploadFile/";
        
        /*
        Generate random file name and add extension
        */
        $fileCustomName             = hash("sha1", basename($fileName)."-".bin2hex(openssl_random_pseudo_bytes(32))).".".$fileExtension;
        
        /*
        Full store file path, which is store in database
        */
        $completeUploadFilePathWithRandomName     = $baseUrl.$folderName.$fileCustomName;
        
        /*
        Here add file name which is send by user
        */
        $completeUploadFilePathWithFileNameSendByUser = $baseUrl.$folderName.$fileNameSendByUser.".".$fileExtension;
        
        /*
        Target directory where move_uploaded_file function call 
        */
        //$targetDirectory     = dirname(__FILE__).'/'.$folderName.$fileNameSendByUser.".".$fileExtension;
        $targetDirectory     = dirname(__FILE__).'/'.$folderName.$fileCustomName;
        /*
        Upload file max size on 10MB
        */
        $fileMaxSize = 10 * 1024 * 1024;
        
        /*
        Check extension is exit in $fileExtensions array
        */
        if (in_array($fileExtension,$fileExtensions)) 
        {
            if ($fileSize > $fileMaxSize) 
            {
                $response['status']     = false;
                $response['message']    = 'Allow File Size 5 MB Only';
                echo json_encode($response);
            }
            else if (file_exists($targetDirectory)) 
            {
                $response['status']     = false;
                $response['message']    = 'Sorry, File Name Already Exists';
                echo json_encode($response);
            }
            else
            {
                try 
                {
                    if (!move_uploaded_file($fileTempName,$targetDirectory)) 
                    {
                        $response['status']     = false;
                        $response['message']    = 'File Uploaded Faild';
                        echo json_encode($response);
                    }
                    else
                    {
                        $insert_query="INSERT INTO files(uploadedByName,fileName,filePath) VALUES ('$uploadedByUserName','$fileNameSendByUser','$completeUploadFilePathWithRandomName')";
                        $query = mysqli_query($connection,$insert_query);
                        
                        if($query)
                        {
                            $response['status']     = true;
                            $response['message']    = 'File Uploaded Successfully';
                            echo json_encode($response);
                        }
                        else
                        {
                            $response['status']     = false;
                            $response['message']    = 'Insert Operation Error';
                            echo json_encode($response);
                        }
                    }
                }
                catch (Exception $e)
                {
                    $response['status']     = false;
                    $response['message']    = $e->getMessage();
                    echo json_encode($response);
                }
            }
        }
        else
        {
            $response['status']     = false;
            $response['message']    = 'Invalid file extension';
            echo json_encode($response);
        }
    }
    else
    {
        $response['status']     = false;
        $response['message']    = 'Connection Faild';
        echo json_encode($response);
    }
}
else
{
    $response['status']     = false;
    $response['message']    = 'Only Post Request Allow';
    echo json_encode($response);
}
?>
