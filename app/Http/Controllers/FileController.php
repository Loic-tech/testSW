<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserFile;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PdfCollection;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class FileController extends Controller
{
    public function createFile(Request $request) {

        
        $user = Auth::user();
        //return $user;

        $fileName= $request->file('path')->getClientOriginalName();
        $file = new UserFile();
        $pdfFile = $fileName;
        $file->path = $pdfFile;
        $file->name_of_file = $fileName;
      
        $file->user()->associate($user);
        $file->save();
        
        $file=UserFile::where('id',$file->id)->first();
        return new PdfCollection($file);
    }

    public function uploadFile($id) {
            $ch = curl_init();
            $url = "https://staging-api.yousign.com/files";
            set_time_limit(200000);

        
            $file = UserFile::where('id', $id)->first();
            $pathFile = base64_encode(file_get_contents($file->path)); 
            $header=[];
            $header[]="Content-Type:application/json";
            $header[]="cache-control: no-cache";
            $header[]="Authorization: Bearer b49512e2e1555a728838bc852fd2afde";
            
            $post = array(
                "name" => "The best name for my file.pdf",
                "content" => $pathFile
            );
            $value = json_encode($post);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $value);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            header("Access-Control-Allow-Origin: *");
            $result = curl_exec($ch);
            
             curl_close ($ch);
            return json_decode($result,true);
    }

    public function secondprocess($id) {
        $ch = curl_init();
        $url = "https://staging-api.yousign.com/procedures";
        $value = self::uploadFile($id);    
        $elements = array(
            "name" => "My first Procedure",
            "description" => "Awesome! Here is the description of my first procedure",
            "members"=> [[
                    "firstname" => "Loïc",
                    "lastname" => "Gnagoh",
                    "email" => "loicgnagoh@email.com",
                    "phone"=> "+21658693367",
                    "fileObjects" => [[
                                "file"=> $value['id'],
                                "page"=> 2,
                                "position"=> "230,499,464,589",
                                "mention"=> "Read and approved",
                                "mention2"=> "Signed by John Doe"          
                            ]]
        ]]
        );
        $header=[];
        $header[]="Content-Type:application/json";
        $header[]="cache-control: no-cache";
        $header[]="Authorization: Bearer b49512e2e1555a728838bc852fd2afde";
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($elements));
        header("Access-Control-Allow-Origin: *");
        $result = curl_exec($ch);
        curl_close ($ch);
        return json_decode($result,true);; 
    }

    public function getMembers($id) {
        $getElement = self::secondprocess($id);
        $transition = $getElement['members'];
        foreach($transition as $item) {
            $memberid = $item['id'];
        }
        return $memberid;
    }

    public function test ($id) {
        $data = self::getMembers($id);

        return view('yousign', ['id' => $data]);
    }
}
