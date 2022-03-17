<?php
namespace App\Controllers;
use App\Models\Model;
use Exception;

class Api{

    public static function getAll($request)
    {
        try{
            $paises = Model::getAll();
            if($paises == null){
                return [
                    "error" => "Falha ao obter dados"
                ];
            }
            return $paises;
        }catch(Exception $ex)
        {
            return [
                "error" => "Falha ao obter dados"
            ];
        }

    }

    public static function getByname($request,$name)
    {
        try{
            $pais = Model::getByname($name);
            if($pais!= null){
                return $pais;
            }
            return [
                "error" => "Pais não Encontrado"
            ];
            
        }catch(Exception $ex)
        {
            return [
                "error" => "Pais não Encontrado"
            ];
        }
    }

}