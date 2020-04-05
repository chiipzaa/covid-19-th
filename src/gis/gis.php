<?php
require('./../../config/config.inc.php');
require_once('./../../includes/connectDB.php');
require('./../../includes/function.ini.php');
require("./../lib.inc.php");
list($kpi,$rate)=0;
$vars = array_merge($_GET, $_POST);
array_walk_recursive($vars, function (&$item, $key) {
    $item = addslashes($item);
});
extract($vars);

$sql="SELECT level as config_level,provincecode as config_area FROM sys_config limit 1";
try {
    $query = $db->prepare($sql);
    $query->execute();
    $rows_data = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}
extract($rows_data[0]);

$gis_year = ($byear - 543);
$id_gis = $reportid;
if($config_level==1 && $selAllProv==1){
    $groupby="zonecode";
}else if($config_level==1 && $selAllProv==2){
    $groupby="cc";
}else if($config_level==2){
    $groupby="cc";
}else if($config_level==3){
    $groupby="ccaa";
}

$sql2="SELECT report_name,source_table,notice FROM sys_report WHERE id='".$reportid."' ";
//echo $sql2;
try {
    $query2 = $db->prepare($sql2);
    $query2->execute();
    $rows_data2 = $query2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}
extract($rows_data2[0]);

$sql3="SELECT option_code as option_gis,option_name,sql_gis,color as jsoncolor, IFNULL(level, 'NULL') AS jsonlevel,IFNULL(kpi,0) AS kpi,IFNULL(rate,0) AS rate 
        FROM sys_gis WHERE id='".$reportid."' AND option_code='".$option_gis."' 
        AND active=1 ORDER BY option_code ASC ";
try {
    $query3 = $db->prepare($sql3);
    $query3->execute();
    $rows_data3 = $query3->fetchAll(PDO::FETCH_ASSOC);
    $rcount= $query3->rowCount();
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}
extract($rows_data3[0]);
$jsoncolor=stripcslashes($jsoncolor);
$color="";
$lowerlimit="";
$legend="";
$colorjson=json_decode($jsoncolor, true);
$color=$colorjson[0];
$count_color=count($color);
$master_sql=trim($sql_gis);
$sql4= str_ireplace("{tablename}", $source_table, trim($master_sql));
$sql4 = str_ireplace("{year}", $byear, trim($sql4));
$sql4 = str_ireplace("{reportid}", $reportid, trim($sql4));
 try {
    $query4 = $db->prepare($sql4);
    $query4->setFetchMode(PDO::FETCH_LAZY);
    $query4->execute();
   
    $query4 = explode(';',$sql4);
    $count_q= count($query4)-1;
    $sql5=$query4[$count_q]; 
    $sql6 =$sql5." GROUP BY ".$groupby." ORDER BY gis_val DESC LIMIT 1";
   
    $query6 = $db->prepare($sql6);
    $query6->execute();
    $rows_data6 = $query6->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $pe) {
     die("Could not connect to the database $dbname :" . $pe->getMessage());
 }
 $sql7 =$sql5." GROUP BY ".$groupby." ORDER BY gis_val ASC LIMIT 1";
 $query7 = $db->prepare($sql7);
 $query7->execute();
 $rows_data7 = $query7->fetchAll(PDO::FETCH_ASSOC);


 extract($rows_data6[0]);
 $max_value=$gis_val;
 extract($rows_data7[0]);
 $min_value=$gis_val;
 $k_value=($max_value-$min_value)/$count_color;

if($kpi > 0 && count($color)==2){
    $lowerlimit[0] = 0;        
    $lowerlimit[1] = floor($kpi);
    $legend[0] =number_format(0,2);
    $legend[1] =number_format($kpi,2);
}else{
    // $lowerlimit[0] = 0;        
    // $legend[0] = 0;
    $lowerlimit[0] = floor($min_value);        
    $legend[0] = floor($min_value);
    for($i=1;$i<=$count_color;$i++){
            $lowerlimit[$i] = floor($min_value+($i*$k_value));        
            $legend[$i] = floor($min_value+($i*$k_value));
        }
 }
$setcolor = "";
if($jsonlevel != "NULL"){
	$jsonlevel=stripcslashes($jsonlevel);
	$jsonlevel=json_decode($jsonlevel, true);
	$level = $jsonlevel[0];
	$leveltxt = $jsonlevel[1];
}

for ($x = count($color)-1; $x >= 0; $x--) {
	if($jsonlevel != "NULL"){
		if($x==count($color)-1){
            $setcolor .=  "d >= ".$level[$x]."  ? '".$color[$x]."' :";
        }else{
            $setcolor .=  "d >= ".$level[$x]." && d < ".$level[($x+1)]."  ? '".$color[$x]."' :";
        }    
        if($x==0){
            $setcolor .=  "'".$color[$x]."' ;";
        }
	}else if($kpi > 0 && count($color)==2){
    if($x==count($color)-1){
        $setcolor .=  "d >= ".$lowerlimit[$x]."  ? '".$color[$x]."' :";
    }else{
        $setcolor .=  "'".$color[$x]."' ;";
    }    
    }else{
        if($x==count($color)-1){
            $setcolor .=  "d >= ".$lowerlimit[$x]."  ? '".$color[$x]."' :";
        }else{
            $setcolor .=  "d >= ".$lowerlimit[$x]." && d < ".$lowerlimit[($x+1)]."  ? '".$color[$x]."' :";
        }    
        if($x==0){
            $setcolor .=  "'".$color[$x]."' ;";
        }
    }
} 
$setlegend = "";
for ($x = count($color) - 1; $x >= 0; $x--) {
	if($jsonlevel != "NULL"){

		$setlegend .= 'labels.push("' . "<i style='background:" . $color[$x] . "'></i> " . $leveltxt[$x] .'");';
	}else if($kpi > 0 && count($color)==2){
        $setlegend .= 'labels.push("' . "<i style='background:" . $color[$x] . "'></i>&#95; " . $legend[$x] .'");';
    }else{
        $setlegend .= 'labels.push("' . "<i style='background:" . $color[$x] . "'></i>&#95; " . $legend[$x] .'");';
    }
}
?>

    <div class="row"> 
        <div id="map" style="width: 100%; height:720px; border: none; "></div>
    </div>
 <script>
       //map  
        var geojson ,pjson,ampjson ,hjson ,type ,id ,zone ,labelLoading;
        //var googleLayer ,osm;
        var startPoint = [13.736717, 100.523186];
        var map = L.map('map', {editable:false,dragging:false,maxZoom:16,minZoom:6,doubleClickZoom:false,
                        touchZoom:false,scrollWheelZoom:false,tap:false
                        }).setView(startPoint, 0),
                tilelayer = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', 
                {maxZoom: 20, attribution: 'Data \u00a9 <a href="https://www.openstreetmap.org/copyright"> OpenStreetMap Contributors </a> Tiles \u00a9 HOT'}
                ).addTo(map);
        // var map = L.map('map',{scrollWheelZoom:false});
        //     map.options.maxZoom = 15;
        //     map.options.minZoom = 6;
        //    var _0xc585=["\x6F\x62\x6A\x65\x63\x74","\x52\x4F\x41\x44\x4D\x41\x50","\x61\x64\x64\x4C\x61\x79\x65\x72","\x68\x74\x74\x70\x3A\x2F\x2F\x7B\x73\x7D\x2E\x74\x69\x6C\x65\x2E\x6F\x73\x6D\x2E\x6F\x72\x67\x2F\x7B\x7A\x7D\x2F\x7B\x78\x7D\x2F\x7B\x79\x7D\x2E\x70\x6E\x67","\x74\x69\x6C\x65\x4C\x61\x79\x65\x72","\x61\x64\x64\x54\x6F"];if( typeof google===_0xc585[0]){googleLayer= new L.Google(_0xc585[1]);if( typeof googleLayer===_0xc585[0]){map[_0xc585[2]](googleLayer)};}else {osm=L[_0xc585[4]](_0xc585[3],{});if( typeof osm===_0xc585[0]){osm[_0xc585[5]](map)};};

		// control that shows state info on hover
		var label = new L.Label();
		var info = L.control();
               info.onAdd = function (map) {
			this._div = L.DomUtil.create('div', 'info');
			this.update();
			return this._div;
		};

		info.update = function (props) {    
                        this._div.innerHTML =  (props ?
                                    '<h4>'+ props.name+"["+props.id +']</h4>'  + props.data +" "+ '</sup>'
                                    : 'เลื่อน mouse over แผนที่ <br/>');

		};
                
		info.addTo(map);
		// get color depending on data value
		function getColor(d) {
                    return <?php echo $setcolor; ?>
		}

		function style(feature) {
			return {
				weight: 2,
				opacity: 1,
				color: 'white',
				dashArray: '3',
				fillOpacity: 1.0,
				fillColor: getColor(feature.properties.data)
			};
		}

		function highlightFeature(e) {
			var layer = e.target;
			label.setContent("<h4>"+e.target.feature.properties.name+"</h4>"+e.target.feature.properties.data);
                        label.setLatLng(e.latlng);
                        map.showLabel(label);
			layer.setStyle({
				weight: 3,
				color: '#666600',
				dashArray: '',
				fillOpacity: 0.2
			});
			if (!L.Browser.ie && !L.Browser.opera) {
				layer.bringToFront();
			}

			info.update(layer.feature.properties);
		}

		 
		function resetHighlight(e) {
			geojson.resetStyle(e.target);
			info.update();
		}

		function zoomToFeature(e) {
                    id =e.target.feature.properties.id ;
                    type = (e.target.feature.properties.type *1)+1 ;
                    if(type <4){
                        var _0xc4ea=["\x67\x65\x74\x42\x6F\x75\x6E\x64\x73","\x74\x61\x72\x67\x65\x74","\x66\x69\x74\x42\x6F\x75\x6E\x64\x73","\x67\x65\x74\x43\x65\x6E\x74\x65\x72","\x3C\x64\x69\x76\x3E\x20\x3C\x69\x6D\x67\x20\x73\x72\x63\x3D\x22\x69\x6D\x67\x2F\x62\x67\x2D\x73\x70\x69\x6E\x6E\x65\x72\x2E\x67\x69\x66\x22\x20\x61\x6C\x74\x3D\x22\x4C\x6F\x61\x64\x69\x6E\x67\x22\x20\x68\x65\x69\x67\x68\x74\x3D\x22\x34\x32\x22\x20\x77\x69\x64\x74\x68\x3D\x22\x34\x32\x22\x3E\x20\x3C\x2F\x64\x69\x76\x3E\x3C\x64\x69\x76\x20\x73\x74\x79\x6C\x65\x3D\x22\x63\x6F\x6C\x6F\x72\x3A\x62\x6C\x75\x65\x3B\x22\x20\x3E\x4C\x6F\x61\x64\x69\x6E\x67\x3C\x2F\x64\x69\x76","\x61\x64\x64\x4C\x61\x79\x65\x72"];map[_0xc4ea[2]](e[_0xc4ea[1]][_0xc4ea[0]]());var labelLocation=e[_0xc4ea[1]][_0xc4ea[0]]()[_0xc4ea[3]]();labelLoading= new L.LabelOverlay(labelLocation,_0xc4ea[4]);map[_0xc4ea[5]](labelLoading);
                        //map    
                        if(type=="2"){
                            if(typeof pjson ==="object"){
                                map.removeLayer(pjson);
                            }   
                            $.ajax({
                                dataType: "json",
                                url: 'gis/geojson.php?a='+id+'&t=2&r=<?php echo $id_gis; ?>&year=<?php echo $gis_year; ?>&o=<?php echo $option_gis; ?>',
                                success: function(data) {
                                var _0x2690=["\x62\x72\x69\x6E\x67\x54\x6F\x46\x72\x6F\x6E\x74","\x61\x64\x64\x54\x6F","\x67\x65\x6F\x4A\x73\x6F\x6E","\x72\x65\x6D\x6F\x76\x65\x4C\x61\x79\x65\x72","\x67\x65\x74\x5A\x6F\x6F\x6D"];pjson=L[_0x2690[2]](data,{style:style,onEachFeature:onEachFeature})[_0x2690[1]](map)[_0x2690[0]]();map[_0x2690[3]](geojson);zoomlevel=map[_0x2690[4]]();map[_0x2690[3]](labelLoading);
                            }
                            }).error(function() {});   

                        }
                        if(type=="3"){
                            zone =e.target.feature.properties.zone ;
                            if(typeof pjson ==="object"){
                                map.removeLayer(pjson);
                            }
                            if(typeof ampjson ==="object"){
                                map.removeLayer(ampjson);
                            }    
                            $.ajax({
                                dataType: "json",
                                url: 'gis/geojson.php?a='+id+'&t=3&r=<?php echo $id_gis; ?>&year=<?php echo $gis_year; ?>&year=<?php echo $gis_year; ?>&o=<?php echo $option_gis; ?>',
                                success: function(data) {
                                    var _0xbc81=["\x62\x72\x69\x6E\x67\x54\x6F\x46\x72\x6F\x6E\x74","\x61\x64\x64\x54\x6F","\x67\x65\x6F\x4A\x73\x6F\x6E","\x72\x65\x6D\x6F\x76\x65\x4C\x61\x79\x65\x72","\x67\x65\x74\x5A\x6F\x6F\x6D"];ampjson=L[_0xbc81[2]](data,{style:style,onEachFeature:onEachFeature})[_0xbc81[1]](map)[_0xbc81[0]]();map[_0xbc81[3]](geojson);zoomlevel=map[_0xbc81[4]]();map[_0xbc81[3]](labelLoading);
                                }
                            }).error(function() {});   
                        } else{
                            map.removeLayer(labelLoading);
                        } 
                        //hospital layer
                        if(type=="4"){
                            if(typeof pjson ==="object"){
                                map.removeLayer(pjson);
                            }
                            if(typeof ampjson ==="object"){
                                map.removeLayer(ampjson);
                                ampjson.setStyle({fillColor:""});
                            }  
                            $.ajax({
                                dataType: "json",
                                url: 'gis/geojson.php?a='+id+'&t=4&r=<?php echo $id_gis; ?>&year=<?php echo $gis_year; ?>&o=<?php echo $option_gis; ?>',
                                success: function(data) {
                                    var _0x6192=["\x62\x72\x69\x6E\x67\x54\x6F\x46\x72\x6F\x6E\x74","\x61\x64\x64\x54\x6F","\x63\x69\x72\x63\x6C\x65\x4D\x61\x72\x6B\x65\x72","\x67\x65\x6F\x4A\x73\x6F\x6E","\x72\x65\x6D\x6F\x76\x65\x4C\x61\x79\x65\x72","\x67\x65\x74\x5A\x6F\x6F\x6D"];hjson=L[_0x6192[3]](data,{style:style,pointToLayer:function(_0xba7fx1,_0xba7fx2){return L[_0x6192[2]](_0xba7fx2,{radius:10,fillOpacity:0.85})},onEachFeature:onEachFeature})[_0x6192[1]](map)[_0x6192[0]]();map[_0x6192[4]](geojson);zoomlevel=map[_0x6192[5]]();map[_0x6192[4]](labelLoading);
                                }
                                }).error(function() {});

                        } else{
                            map.removeLayer(labelLoading);
                        }  
                    }
		}
		function onEachFeature(feature, layer) {
			layer.on({
				mouseover: highlightFeature,
				mouseout: resetHighlight,
				click: zoomToFeature
			});
		}
                if($("#selAllProv").val()==2){
                    var urlgo='gis/geojson.php?a=<?php echo $config_area; ?>&s=<?php echo $selAllProv;?>&t=2&r=<?php echo $id_gis; ?>&year=<?php echo $gis_year; ?>&o=<?php echo $option_gis; ?>';
                }else{
                    var urlgo='gis/geojson.php?a=<?php echo $config_area; ?>&s=<?php echo $selAllProv;?>&t=<?php echo $config_level; ?>&r=<?php echo $id_gis; ?>&year=<?php echo $gis_year; ?>&o=<?php echo $option_gis; ?>';
                }
                $.ajax({
                    dataType: "json",
                    url:urlgo ,
                    success: function(data) {
                        geojson = L.geoJson(data, {
                                style: style,
                                onEachFeature: onEachFeature
                        }).addTo(map);

                        map.fitBounds(geojson.getBounds());
                    }
                    }).error(function() {});

                map.attributionControl.addAttribution('HDC &copy; <a href="https://www.moph.go.th/">กระทรวงสาธารณสุข</a>');
                var legend = L.control({position: 'bottomright'});
		
        legend.onAdd = function (map) {
                    var div = L.DomUtil.create('div', 'info legend');
                    var labels = [];
                       <?php echo $setlegend; ?> 
			div.innerHTML = labels.join('<br>');
			return div;
		};

		legend.addTo(map);
                //
                var _0x7f65=["\x74\x6F\x70\x6C\x65\x66\x74","\x63\x6F\x6E\x74\x72\x6F\x6C","\x6F\x6E\x41\x64\x64","\x64\x69\x76","","\x63\x72\x65\x61\x74\x65","\x44\x6F\x6D\x55\x74\x69\x6C","\x69\x6E\x6E\x65\x72\x48\x54\x4D\x4C","\x3C\x64\x69\x76\x3E\x3C\x62\x75\x74\x74\x6F\x6E\x20\x69\x64\x3D\x27\x62\x74\x6E\x5F\x62\x61\x63\x6B\x27\x3E\u0E22\u0E49\u0E2D\u0E19\u0E01\u0E25\u0E31\u0E1A\x3C\x2F\x62\x75\x74\x74\x6F\x6E\x3E\x3C\x2F\x64\x69\x76\x3E","\x61\x64\x64\x54\x6F"];var layerControl=L[_0x7f65[1]]({position:_0x7f65[0]});layerControl[_0x7f65[2]]=function(_0x96f8x2){var _0x96f8x3=L[_0x7f65[6]][_0x7f65[5]](_0x7f65[3],_0x7f65[4]);_0x96f8x3[_0x7f65[7]]=_0x7f65[8];return _0x96f8x3;};layerControl[_0x7f65[9]](map);
                //
                map.on("zoomend", function (e) { 
                    if (map.getZoom() === 6) {
                        if(typeof ampjson ==="object"){
                            map.removeLayer(ampjson);
                        }
                        if(typeof pjson ==="object"){
                            map.removeLayer(pjson);
                        }
                    }    
                }); 
                
                var _0xb64a=["\x63\x6C\x69\x63\x6B","\x61\x64\x64\x45\x76\x65\x6E\x74\x4C\x69\x73\x74\x65\x6E\x65\x72","\x62\x74\x6E\x5F\x62\x61\x63\x6B","\x67\x65\x74\x45\x6C\x65\x6D\x65\x6E\x74\x42\x79\x49\x64"];document[_0xb64a[3]](_0xb64a[2])[_0xb64a[1]](_0xb64a[0],function(){goBack()});
                
                function goBack(){
                    if(typeof hjson ==="object"){
                        map.removeLayer(hjson);
                        hjson="";
                        if(typeof ampjson ==="object"){
                            ampjson.setStyle(style);
                            map.addLayer(ampjson);
                            map.fitBounds(ampjson.getBounds());
                        
                            type = type -1;
                            if(type=="3"){
                                var p = id.substring(0,2);
                            }
                        }    
                    }else if(typeof ampjson ==="object"){
                        map.removeLayer(ampjson);
                        if(typeof pjson ==="object"){
                            ampjson="";
                            map.addLayer(pjson);
                            map.fitBounds(pjson.getBounds());  
                            type = type -1;
                            if(type=="3"){
                                var p = id.substring(0,2);
                            }
                        }else{
                            map.removeLayer(ampjson);
                            map.addLayer(geojson);
                            map.fitBounds(geojson.getBounds());
                        }    
                    }else{
                        if(typeof pjson ==="object"){
                            map.removeLayer(pjson);
                        }    
                        map.addLayer(geojson);
                        map.fitBounds(geojson.getBounds());
                    }    
                }    
    
    </script>