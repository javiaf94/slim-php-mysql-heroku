<?php


class CSVController
{
    public static function AgregarCSV($path, $texto)
    {            
        $archivo = fopen($path, "a");
        fputs($archivo, $texto);
        fputs($archivo, PHP_EOL);
        fclose($archivo);
    }

    public static function LeerCSV($path)
    {
        $archivo = fopen($path, "r");
        $arr = array();
        if($archivo)
        {
            while(!feof($archivo))
            {
                $linea = fgets($archivo) ;
                if($linea != "")
                {
                    array_push($arr,$linea);
                }
            }
        }
        else
        {
            echo 'No pudo leerse el archivo';
        }
        fclose($archivo);
        
        return $arr;
    }    
}


?>