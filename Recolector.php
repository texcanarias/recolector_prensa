<?php

//Por defecto vamos a especificar HTML5 con utf8
echo "<html><head><meta charset='UTF-8' /></head>";

error_reporting(E_ALL);
ini_set('display_errors', '1');

class RssItem{
    public $Nombre;
    public $FechaPublicacion;
    public $Enlace;
    public $Titulo;

    function __tostring(){
        return $this->Nombre." ".$this->FechaPublicacion." ".$this->Enlace." ".$this->Titulo.PHP_EOL;
    }
}

//
/*
 * Recoge las entradas RSS y las mete en una tabla de la base de datos.
 *
 * http://feeds.dzone.com/dzone/frontpage?format=xml
 */

$Url = array();
$Url[] = array("Nombre" => "elpais-portada", "Url"=>"http://ep00.epimg.net/rss/elpais/portada.xml");
$Url[] = array("Nombre" => "elmundo-portada", "Url"=>"http://estaticos.elmundo.es/elmundo/rss/portada.xml");
$Url[] = array("Nombre" => "abc-portada", "Url" => "http://www.abc.es/rss/feeds/abcPortada.xml");

$enlace = conectar();

for($Cont=0; $Cont<count($Url); ++$Cont){
    $rss_xml = file_get_contents($Url[$Cont]["Url"]);
    $est_xml = simplexml_load_string($rss_xml);
    $VectorEntrada = array();
    $VectorEntrada = $est_xml->channel->item;
    $ContVectorEntrada = count($VectorEntrada);

    $Fuente = $Url[$Cont]["Nombre"];
    $UltimaFecha = getUltimaFechaPublicacion($enlace, $Fuente);
    echo "RSS de ".$Fuente.PHP_EOL;

    for($ContI=0; $ContI<$ContVectorEntrada; ++$ContI){
        $Datos = new RssItem();
	       $Datos->Nombre= $Fuente;
        $Datos->FechaPublicacion =  date( 'Y-m-d H:i:s',strtotime($VectorEntrada[$ContI]->pubDate));
        $Datos->Enlace = $VectorEntrada[$ContI]->link;
        $Datos->Titulo = $VectorEntrada[$ContI]->title;

        if($Datos->FechaPublicacion > $UltimaFecha){
            $IdRss = registarItemRss($enlace, $Datos);

            for($i=0,$t=count($VectorEntrada[$ContI]->category);$i<$t;++$i){
                $tag = trim(strtolower($VectorEntrada[$ContI]->category[$i]));
                $IdTag = registrarORecuperarIdTag($enlace, $tag);
                registrarVinculo($enlace,$IdRss, $IdTag);
            }
        }
    }
}

cerrar($enlace);

return false;



function conectar(){
    require_once ("Persistencia.conf");
    $enlace = mysqli_connect($Server, $User, $Pass, $Database);

    if (!$enlace) {
        echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
        echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
        echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }

    mysqli_set_charset($enlace,"utf8");
    return $enlace;
}

function cerrar($enlace){
    mysqli_close($enlace);
}

function registarItemRss($enlace, RssItem $Item){
    $Sql = "INSERT INTO
                rss_base_item
             SET
                Fuente = '".$Item->Nombre."',
                FechaPublicacion = '".$Item->FechaPublicacion."',
                Url = '".$Item->Enlace."',
                Titulo = '".$Item->Titulo."' ";
    if (!$resultado = $enlace->query($Sql)){
        echo "Error en registarItemRss". PHP_EOL;
        echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
        echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    }
    else{
        return $enlace->insert_id;
    }
}

function registrarORecuperarIdTag($enlace, $tag){
    $Id = recuperarTag($enlace, $tag);
    $NoExisteTag = !(0 < $Id);
    if ($NoExisteTag){
        $Id = registrarTag($enlace, $tag);
    }
    return $Id;
}

function recuperarTag($enlace, $tag){
    $Sql = "SELECT
                Id
            FROM
                rss_base_tag
            WHERE
                Nombre = '".$tag."'
            LIMIT 1 ";
    $Id = 0;
    if ($resultado = mysqli_query($enlace, $Sql)) {
        $row = $resultado->fetch_array(MYSQLI_ASSOC);
        $Id = $row['Id'];
        mysqli_free_result($resultado);
    }
    return $Id;
}

function registrarTag($enlace,$tag){
    $Sql = "INSERT INTO
                rss_base_tag
             SET
                Nombre = '".$tag."' ";
    if (!$resultado = $enlace->query($Sql)){
        echo "Error en registrarTag". PHP_EOL;
        echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
        echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    }
    else{
        return $enlace->insert_id;
    }

}

function registrarVinculo($enlace,$IdRss, $IdTag){
   $Sql = "INSERT INTO
                rss_base_item_rel_tag
             SET
                rss_base_item_Id = ".$IdRss.",
                rss_base_tag_Id = ".$IdTag;
    if (!$resultado = $enlace->query($Sql)){
        echo "Error en registrar Vinculo". PHP_EOL;
        echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
        echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
        echo $Sql.PHP_EOL;
    }
}

function getUltimaFechaPublicacion($enlace, $Fuente){
    $Sql = "SELECT
                FechaPublicacion
            FROM
                rss_base_item
            WHERE
                Fuente='".$Fuente."'
            ORDER BY
                FechaPublicacion DESC
            LIMIT 1";
    $Fecha = "";
    if ($resultado = mysqli_query($enlace, $Sql)) {
        $row = $resultado->fetch_array(MYSQLI_ASSOC);
        $Fecha = $row['FechaPublicacion'];
        mysqli_free_result($resultado);
    }
    return $Fecha;

}