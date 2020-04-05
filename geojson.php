<?php 
//error_reporting(0);
// require_once('includes/connectDB.php');
require_once('src/lib/db.php');

$vars = array_merge($_GET, $_POST);
 array_walk_recursive($vars, function (&$item, $key) {
     $item = addslashes($item);
 });
$a="";
$t="2";
extract($vars);

// print_r($vars);
// echo "t:".$t.",a:".$a.",s:".$s."d:".$d."<br>";
// $year = $year+543;
// $reportid = $r;

// $sql2 = sprintf("SELECT * FROM sys_report WHERE id ='%s' ",$reportid);
// //echo $sql2;
// try {
//     $query2 = $db->prepare($sql2);
//     $query2->execute();
//     $rows_data2 = $query2->fetchAll(PDO::FETCH_ASSOC);
// } catch (PDOException $pe) {
//     die("Could not connect to the database $dbname :" . $pe->getMessage());
// }
// extract($rows_data2[0]);
//          if(number_format($rate) >0){ $kpi_rate = $rate;}else{$kpi_rate = 100;}			

// //if(!$o){$o=0;}
// $sql3="SELECT option_code,option_name,sql_gis,color as jsoncolor 
//         FROM sys_gis WHERE id='".$reportid."' AND active=1 AND option_code='".$o."'";
// //echo $sql3;
// try {
//     $query3 = $db->prepare($sql3);
//     $query3->execute();
//     $rows_data3 = $query3->fetchAll(PDO::FETCH_ASSOC);
// } catch (PDOException $pe) {
//     die("Could not connect to the database $dbname :" . $pe->getMessage());
// }
//  extract($rows_data3[0]);
//  $master_sql=$sql_gis;
//  $sql4= str_ireplace("{tablename}", $source_table, trim($master_sql));
//  $sql4 = str_ireplace("{year}", $year, trim($sql4));
//  $sql4 = str_ireplace("{reportid}", $reportid, trim($sql4));
//  $query4 = $db->prepare($sql4);
//  $query4->setFetchMode(PDO::FETCH_LAZY);
//  $query4->execute();

//  $query4 = explode(';',$sql4);
//  $count_q= count($query4)-1;
//  $sql5=$query4[$count_q]; 

if($t=="1"){ //country ->mophzone
        // $sql5=$sql5." GROUP BY zonecode ";
        // $sql_data="SELECT s.*,s.zonecode as areacode FROM (".$sql5.") as s";
        $sql_map = sprintf("SELECT areacode , concat('เขต ',areacode*1) as areaname ,geojson ,areatype FROM  geojson WHERE areatype='%s' ", $t );
}elseif($t=="2"){ //country ->province
    if($s==1){
        // $sql5=$sql5." GROUP BY cc HAVING zonecode='".$a."' ";
        // $sql_data="SELECT s.*,s.cc as areacode FROM (".$sql5.") as s";
    }else if($s==2){
        // $sql5=$sql5." GROUP BY cc ";
        // $sql_data="SELECT s.*,s.cc as areacode FROM (".$sql5.") as s";
    }
    if(strlen(trim($a))==0){
        $sql_map = sprintf("SELECT areacode , changwatname as areaname ,geojson ,zonecode ,areatype FROM cchangwat "
                ." INNER JOIN geojson ON areacode=changwatcode AND areatype='%s' "
                ." WHERE changwatcode between '10' AND '96' ", $t );
    }elseif(strlen(trim($a))==2){ //mophzone ->province
        $sql_map = sprintf("SELECT areacode , changwatname as areaname ,geojson ,zonecode,areatype FROM cchangwat "
                ." INNER JOIN geojson ON areacode=changwatcode AND areatype='%s' "
                ." WHERE zonecode = '%s' ",$t, $a );
    }
}elseif($t=="3"){ //province -> ampur
        // $sql5=$sql5." GROUP BY ccaa HAVING cc='".$a."' ";
        // $sql_data="SELECT s.*,s.ccaa as areacode FROM (".$sql5.") as s";

    if(strlen(trim($a))==2){
        $sql_map = sprintf("SELECT areacode , ampurname as areaname ,areatype ,geojson ,campur.changwatcode ,changwatname FROM campur "
                ." INNER JOIN cchangwat ON cchangwat.changwatcode=campur.changwatcode "
                ." INNER JOIN geojson ON areacode=ampurcodefull AND areatype='%s' AND length(geojson)>0 AND cchangwat.changwatcode='%s'"
                ." ORDER BY changwatcode ,areacode " ,$t,$a);
    }
}elseif($t=="4"){ // -> ampur-> hospital
	  if(strlen(trim($a))==4){
        // $sql5=$sql5." GROUP BY hospcode HAVING ccaa='".$a."' ";
        // $sql_data="SELECT s.*,s.hospcode as areacode FROM (".$sql5.") as s";
        $sql_map = sprintf("SELECT hoscode as areacode , hosname as areaname ,geojson.lat ,geojson.lon ,campur.changwatcode ,changwatname,areatype FROM geojson "
                ." INNER JOIN campur ON substring(areacode,1,4)=ampurcodefull AND areatype='%s'  "
                ." INNER JOIN cchangwat ON cchangwat.changwatcode=campur.changwatcode "
                ." INNER JOIN chospital ON chospital.hoscode=geojson.hcode "
                . "WHERE ampurcodefull='%s' "
		." ORDER BY hostype,hoscode " ,$t,$a);
    }
}
//  echo "<pre>";
//  echo $sql_data;
//  echo "</pre>";

// try {
//     $q = $db->query($sql_data);
//     $q->setFetchMode(PDO::FETCH_ASSOC);
//     while ($r = $q->fetch()){
//                 $_data[$r['areacode']] = $r['gis_val'] ;
foreach($d as $k=>$v){
    $_data[$v['c']] = $v['t'] ;
}
// print_r($_data);
//     }

// } catch (PDOException $pe) {
//     die("error:" . $pe->getMessage());
// }
// echo $sql_map;

try {

    $q = $db->query($sql_map);
    $q->setFetchMode(PDO::FETCH_ASSOC);
    while ($r = $q->fetch()){
        $areacode =  $r['areacode'];
        $type[$areacode] = $r['areatype'];
        $areaname[$areacode] = $r['areaname'];
        
        if($t=="2"){
            $zone[$areacode] = $r['zonecode'];
        }
        if($t=="3"){
            $zone[$areacode] = $r['zonecode'];
            $changwat[$areacode] = $r['changwatcode'];
            $areaname[$areacode] =" อ.".$r['areaname']." จ.".$r['changwatname'];
        }
        if($t=="4"){
            $json[$areacode] = sprintf('{"type": "Point","coordinates": [%s,%s]} ',$r['lon'],$r['lat']);
        }else{
            $json[$areacode] = stripslashes($r['geojson']);
        }
        
    }
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}

$j = '{"type":"FeatureCollection","features":[';
// union json and db
foreach($areaname as $k=>$v){
   // $style = "style1";
    if(!$_data[$k]){
        $_data[$k] = " - ";
    }
    $j .= '{"type":"Feature",';
    $j .= ' "properties":{"id":"'.$k.'" ,"type":"'.$type[$k].'", "name":"'.$v.'", "zone":"'.$zone[$k].'" ,"data":"'.$_data[$k].'"},';
    $j .= ' "geometry":'.$json[$k];
    $j .= '},';
}
$j = substr($j, 0, strlen($j)-1);
$j .= "]}";

header('Content-Type: application/json');
echo $j;
?>


