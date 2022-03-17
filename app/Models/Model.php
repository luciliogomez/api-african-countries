<?php
namespace App\Models;

use Exception;

class Model{

    public static function getAll()
    {  

        if( ($content = file_get_contents(__DIR__."/../../paises.json")) ==false ){                                                                                                                                                                            
            return null;
        }
        
        $data = json_decode($content,true);
        
        return $data;
       
    }
    public static function getByname($name)
    {   
        $name = mb_strtolower(str_replace("_","",$name));
        $name = urldecode($name);
        try{
            $paises = self::getAll();
            foreach($paises as $pais => $details){
                $countryName = str_replace(" ","",trim(mb_strtolower($details['pais'])));

                if($name == $countryName){   
                    return $details;
                }
                
            }
            return null;
            
        }catch(Exception $ex)
        {
            return null;
        }
    }
}