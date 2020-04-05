<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Teerasit">
  <title>COVID-19</title>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">

  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
    integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
  </script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
    integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"
    integrity="sha256-h20CPZ0QyXlBuAw7A+KluUYx/3pK+c7lYEpqLTlxjYQ=" crossorigin="anonymous" />

  <link rel="stylesheet" href="src/gis/plugins/map/lib/leaflet.css" type="text/css" />
  <link rel="stylesheet" href="src/gis/plugins/map/lib/map.css" type="text/css" />
  <link href="src/gis/plugins/map/lib/leaflet.label.css" rel="stylesheet" type="text/css" />



  <style>
  .bd-placeholder-img {
    font-size: 1.125rem;
    text-anchor: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }

  @media (min-width: 768px) {
    .bd-placeholder-img-lg {
      font-size: 3.5rem;
    }
  }

  .gi-2x {
    font-size: 2em;
  }

  .gi-3x {
    font-size: 3em;
  }

  .gi-4x {
    font-size: 4em;
  }

  .gi-5x {
    font-size: 5em;
  }
  </style>
  <link href="src/css/css.css" rel="stylesheet">
</head>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$gis_year = "2020";

require ('src/lib/db.php');

$ThaiMonth=array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
$arrmonthnames = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฎาคม', '08'=>'สิงหาคม', '09'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

function exportTable($divid , $tableid){
	$tableName="Covid-19";

	  echo   "<div class=\"box pull-right\" style=\"height:50px;\" >";
		echo "<div id=\"save-icon\" class=\"btn-group\" >";
		
		echo "<button type=\"button\" class=\"btn  btn-success dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" style=\"height:40px;min-width:70px;\" ><i class=\"glyphicon glyphicon-download_alt\">&nbsp;</i> <span class=\"caret\"></span></button>";

		echo "
					<ul class=\"dropdown-menu dropdown-menu-right \" role=\"menu\">
                        <li><a href=\"#\" onClick =\"$('#".$tableid."').tableExport({type:'csv',escape:'false',tableName:'".$tableName."'});\"><img src='themes/icons/csv.png' width='16px' height='16px'> CSV</a></li>
                        <li><a href=\"#\" onClick =\"$('#".$tableid."').tableExport({type:'txt',escape:'false',tableName:'".$tableName."'});\"><img src='themes/icons/txt.png' width='16px' height='16px'> TXT</a></li>
                        <li class=\"divider\"></li>				
                        <li><a href=\"#\" onClick =\"$('#".$tableid."').tableExport({type:'excel',escape:'false',tableName:'".$tableName."'});\"><img src='themes/icons/xls.png' width='16px' height='16px'> XLS</a></li>
                        <li><a href=\"#\" onClick =\"$('#".$tableid."').tableExport({type:'doc',escape:'false',tableName:'".$tableName."'});\"><img src='themes/icons/word.png' width='16px' height='16px'> Word</a></li> 
					</ul>
            ";
			echo "</div>";
			echo "</div>";
}

function ThaiDate_dmY($InputDate)
{
	global $ThaiMonth;
	if(strlen($InputDate)>7){
		$day=substr($InputDate,1,2);
        $month=substr($InputDate,3,2);
        $month=(int)$month-1;
		$year=substr($InputDate,6,4);
		$year=$year+543;
		$month=$ThaiMonth[$month];
		$thaidatenew= (int)$day." ".$month." ".$year;
	}else{
		$thaidatenew= "";
	}
	return $thaidatenew;
} 

function GET_Service_Covid19($action){
  $url="https://covid19.th-stat.com/api/open/";
  // if($action="today"){
      $url .=$action;
  // }
  $headers = array(
  'Accept: application/json',
  'Content-Type: application/x-www-form-urlencoded',
  );

  $client = curl_init();
  curl_setopt($client, CURLOPT_URL, $url);
  curl_setopt($client, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($client, CURLOPT_HTTPHEADER, $headers);	
  curl_setopt($client, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($client, CURLOPT_SSL_VERIFYHOST,  false);	
  curl_setopt($client,CURLOPT_RETURNTRANSFER,true);
  $response = curl_exec($client);		
  if (curl_errno ( $client )) {
      echo curl_error ( $client );
      curl_close ( $client );
      exit ();
  }
  curl_close ( $client );
//  echo $response;
  return $response;

}
// function
function objectToArray($d) {
  if (is_object($d)) {
      $d = get_object_vars($d);
  }
  if (is_array($d)) {
      return array_map(__FUNCTION__, $d);
  }
  else {
      return $d;
  }
}

$response=GET_Service_Covid19('today');
$result_array = json_decode($response);	
$rr= objectToArray($result_array);
// print_r($rr);
extract($rr);

$response1=GET_Service_Covid19('cases/sum');
$result_array1 = json_decode($response1);	
$rr1= objectToArray($result_array1);
// print_r($rr1);
extract($rr1);

$query_areacode="SELECT changwatcode,changwatname,changwatname_en  FROM cchangwat ORDER BY changwatcode";
$query = $db->prepare($query_areacode );
$query->execute();
$rows_data = $query->fetchAll(PDO::FETCH_ASSOC);
// var_dump($rows_data);

$size = 78;
$p = 0;
$out = array();
$out1 = array();
$sumcovid = 0;

while($p < $size) {
  foreach($Province as $k => $v){
       if($rows_data[$p]['changwatname_en'] == $k){
           $out[] = array("changwatcode" => $rows_data[$p]['changwatcode']
                   , "changwatname" => $rows_data[$p]['changwatname'], "covid" =>$v);
           $out1[] = $rows_data[$p]['changwatcode'];
           $out2[] = array("c" => $rows_data[$p]['changwatcode'],"t" =>$v);
           $sumcovid +=$v;       
       }
   }
 $p++;
}

foreach($rows_data as $k){
       extract($k);
   if ( !in_array($changwatcode,$out1) ){
       $out[] = array("changwatcode" => $changwatcode ,"changwatname" => $changwatname, "covid" =>0);    
   }
}

sort($out);

$data_json=json_encode($out2,true);
// echo  $data_json;
// echo "<pre>";
// var_dump($out);
// echo "<pre>";

?>

<body>
  <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
    <h5 class="my-0 mr-md-auto font-weight-normal">COVID-19</h5>
    <a class="btn btn-outline-primary" href="https://github.com/chiipzaa/" target="_blank"><i
        class="fab fa-github-alt"></i> Code</a>
  </div>

  <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
    <h1 class="display-4">COVID-19 (TH)</h1>
    <h4 class="text-success">อัพเดทข้อมูลล่าสุด : <?php echo $UpdateDate;?></h4>
  </div>

  <div class="pricing-header mx-auto text-center">
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-12 col-xs-12">
          <div class="card" style="background-color:#ff00cc;min-height:235px;">
            <div class="card-body" style="text-align:center;color:#fff;">
              <span><br></span>
              <h3 class="card-title">ติดเชื้อสะสม</h3>
              <h1 class="card-text"><?php echo number_format($Confirmed);?></h2>
                <span><br></span>
                <h4>
                  <?php 
                            if($NewConfirmed > 0){
                                echo "(+".number_format($NewConfirmed).")"; 
                            }else if($NewConfirmed < 0){
                                echo "(-".number_format(substr($NewConfirmed,1,strlen($NewConfirmed))).")"; 
                            }else{
                                echo "&nbsp;";
                            }
                        ?>
                </h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <br>
    <div class="row col-md-12">

      <div class="col-md-4 ">
        <div class="card" style="background-color:#00572c;">
          <div class="card-body" style="text-align:center;color:#fff;">
            <h4 class="card-title">หายแล้ว</h4>
            <h2 class="card-text"><?php echo number_format($Recovered);?></h3>
              <h5>
                <?php 
                            if($NewRecovered > 0){
                                echo "(+".$NewRecovered.")"; 
                            }else if($NewConfirmed < 0){
                                echo "(-".number_format(substr($NewRecovered,1,strlen($NewRecovered))).")"; 
                            }else{
                                echo "&nbsp;";
                            }
                        ?>
              </h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 ">
        <div class="card" style="background-color:#008080;">
          <div class="card-body" style="text-align:center;color:#fff;">
            <h4 class="card-title">รักษาใน รพ.</h4>
            <h2 class="card-text"><?php echo number_format($Hospitalized);?></h2>
            <h5>
              <?php 
                            if($NewHospitalized > 0){
                                echo "(+".$NewHospitalized.")"; 
                            }else if($NewHospitalized < 0){
                                echo "(-".number_format(substr($NewHospitalized,1,strlen($NewHospitalized))).")"; 
                            }else{
                                echo "&nbsp;";
                            }
                        ?>
            </h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 ">
        <div class="card" style="background-color:#808080;">
          <div class="card-body" style="text-align:center;color:#fff;">
            <h4 class="card-title">เสียชีวิต</h4>
            <h2 class="card-text"><?php echo number_format($Deaths);?></h2>
            <h5>
              <?php 
                            if($NewDeaths > 0){
                                echo "(+".$NewDeaths.")"; 
                            }else if($NewDeaths < 0){
                                echo "(-".number_format(substr($NewDeaths,1,strlen($NewDeaths))).")"; 
                            }else{
                                echo "&nbsp;";
                            }
                        ?>
            </h5>
          </div>
        </div>
      </div>

    </div>
    <hr style="border: 2px solid #808080;border-radius: 5px;">
    <span>
      <h5>&nbsp;&nbsp;จำนวนผู้ติดเชื้อสะสม จำแนกตามจังหวัดที่รักษาพยาบาล</h5>
    </span>

    <div class="row col-md-12">
      <div class="col-md-6">
        <div class="box">
          <div class="box-body">
            <div id="map" style="width:100%; height:720px; border: none; "></div>
            <?php
                    $colorjson=json_decode('[{"0":"#c0c0c0","1":"#f5c9dd","2":"#feabb6","3":"#fe475f",  "4":"#ff0000"}]', true);
                    $color=$colorjson[0];
                    $count_color=count($color);
                    $max_value=5;
                    $min_value=1;
                    $k_value=($max_value-$min_value)/$count_color;

                    $lowerlimit[0] = 0;        
                    $legend[0] =number_format(0,2);

                    for($i=1;$i<=$count_color;$i++){
                            $lowerlimit[$i] = floor($min_value+($i*$k_value));        
                            $legend[$i] = floor($min_value+($i*$k_value));
                    }
                    $setcolor = "";
                    for ($x = count($color)-1; $x >= 0; $x--) {
                        if($x==count($color)-1){
                            $setcolor .=  "d >= ".$lowerlimit[$x]."  ? '".$color[$x]."' :";
                        }else{
                            $setcolor .=  "d >= ".$lowerlimit[$x]." && d < ".$lowerlimit[($x+1)]."  ? '".$color[$x]."' :";
                        }    
                        if($x==0){
                            $setcolor .=  "'".$color[$x]."' ;";
                        }
                    }
                    $setlegend="";
                    for ($x = count($color) - 1; $x >= 0; $x--) {
                        // $setlegend .= 'labels.push("' . "<i style='background:" . $color[$x] . "'></i>".'");';

                        // $setlegend .= 'labels.push("' . "<i style='background:" . $color[$x] . "'></i>&#95; " . $legend[$x] .'");';
                    }
                    ?>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="row col-md-12">
          <div class="col-md-4">
            <div class="card" style="background-color:#0076ec;">
              <div class="card-body" style="text-align:center;color:#fff;">
                <h4 class="card-title card-d">เพศชาย</h4>
                <h2 class="card-text card-d"><?php echo number_format($Gender['Male']);?></h2>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card" style="background-color:#ff00cc;">
              <div class="card-body" style="text-align:center;color:#fff;">
                <h4 class="card-title card-d">เพศหญิง</h4>
                <h2 class="card-text card-d"><?php echo number_format($Gender['Female']);?></h2>
              </div>
            </div>
          </div>
          <div class="col-md-4 ">
            <div class="card" style="background-color:#808080;">
              <div class="card-body" style="text-align:center;color:#fff;">
                <h4 class="card-title card-d">เพศ(ไม่ระบุ)</h4>
                <h2 class="card-text card-d"><?php echo number_format($Gender['Unknown']);?></h2>
              </div>
            </div>
          </div>

        </div>
        <span><br></span>
        <div class="row col-md-12">
          <div class="col-md-6 ">
            <div class="card" style="background-color:#0000a0;">
              <div class="card-body" style="text-align:center;color:#fff;">
                <h4 class="card-title card-d">สัญชาติ ไทย</h4>
                <h2 class="card-text card-d"><?php echo number_format($Nation['Thai']);?></h2>
              </div>
            </div>
          </div>
          <div class="col-md-6 ">
            <div class="card" style="background-color:#808080;">
              <div class="card-body" style="text-align:center;color:#fff;">
                <h4 class="card-title card-d">สัญชาติ อื่นๆ</h4>
                <h2 class="card-text card-d"><?php echo number_format(array_sum($Nation)-$Nation['Thai']);?></h2>
              </div>
            </div>
          </div>
        </div>
        <span><br></span>

        <div class="row col-md-12">
          <div class="col-md-12">
            <div id="ptable" class="box-body table-responsive no-padding">
              <table id="dataTable" class="table table-striped table-bordered nowrap" cellspacing="0" style="width:100%">
                <thead>
                  <th style="text-align:center;">พื้นที่</th>
                  <th style="text-align:center;">จำนวนติดเชื้อสะสม</th>
                </thead>
                <tbody>
                  <?php
                foreach($out as $outx){
                    extract($outx);
                ?>
                  <tr>
                    <td style="text-align:left;"><?php echo $changwatcode.":".$changwatname;?></td>
                    <td style="text-align:center;"><?php echo number_format($covid);?></td>
                  </tr>
                  <?php
                }
                ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td style="text-align:center;"><b>รวม</b></td>
                    <td style="text-align:center;"><b><?php echo number_format($sumcovid);?></b></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>


      </div>


    </div>




  </div>

  <br>
  <br>
  <div class="container">


    <footer class="border-top">

      <div class="row col-12">
        <span class="d-block text-muted">
          <br> ที่มาของข้อมูล: API กรมควบคุมโรค กระทรวงสาธารณสุข http://covid19.ddc.moph.go.th/th
          <br> Modify from HDC on Cloud
          <br>
          <br>

        </span>

      </div>
    </footer>
  </div>
</body>

<script type="text/javascript" src="src/jsTableExport/tableExport.js"></script>
<script type="text/javascript" src="src/jsTableExport/jquery.base64.js"></script>
<script type="text/javascript" src="src/jsTableExport/html2canvas.js"></script>

<script src="src/js/jquery.dataTables.min.js"></script>
<script src="src/js/dataTables.bootstrap.min.js"></script>
<script src="src/js/dataTables.fixedColumns.min.js"></script>

<script src="src/gis/plugins/map/lib/leaflet.js"></script>
<script src="src/gis/plugins/map/lib/leaflet-LabelOverlay.js"></script>
<script src="src/gis/plugins/map/lib/leaflet.label.js" type="text/javascript"></script>

<script>
$(document).ready(function() {
  $('#dataTable').DataTable({
    scrollY: '60vh',
    scrollX: true,
    scrollCollapse: true,
    DeferRender: true,
    AutoWidth: false,
    paging: false,
    info: false,
    searching: false,
    aaSorting: [],
    columnDefs: [
      { "width": "80%" },
      { "width": "20%" },
    ],
    fixedColumns: {
      leftColumns: 1
    }
  });

});
</script>


<script>
/////////////////////////////////////////////////////////////////////map  
var geojson, pjson, ampjson, hjson, type, id, zone, labelLoading;
//var googleLayer ,osm;
var startPoint = [13.236717, 101.423186];
var map = L.map('map', {
    editable: false,
    dragging: false,
    maxZoom: 16,
    minZoom: 6,
    doubleClickZoom: false,
    touchZoom: false,
    scrollWheelZoom: false,
    tap: false
  }).setView(startPoint, 0),
  tilelayer = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
    maxZoom: 20,
    attribution: 'Data \u00a9 <a href="https://www.openstreetmap.org/copyright"> OpenStreetMap Contributors </a> Tiles \u00a9 HOT'
  }).addTo(map);
// var map = L.map('map',{scrollWheelZoom:false});
//     map.options.maxZoom = 15;
//     map.options.minZoom = 6;
//    var _0xc585=["\x6F\x62\x6A\x65\x63\x74","\x52\x4F\x41\x44\x4D\x41\x50","\x61\x64\x64\x4C\x61\x79\x65\x72","\x68\x74\x74\x70\x3A\x2F\x2F\x7B\x73\x7D\x2E\x74\x69\x6C\x65\x2E\x6F\x73\x6D\x2E\x6F\x72\x67\x2F\x7B\x7A\x7D\x2F\x7B\x78\x7D\x2F\x7B\x79\x7D\x2E\x70\x6E\x67","\x74\x69\x6C\x65\x4C\x61\x79\x65\x72","\x61\x64\x64\x54\x6F"];if( typeof google===_0xc585[0]){googleLayer= new L.Google(_0xc585[1]);if( typeof googleLayer===_0xc585[0]){map[_0xc585[2]](googleLayer)};}else {osm=L[_0xc585[4]](_0xc585[3],{});if( typeof osm===_0xc585[0]){osm[_0xc585[5]](map)};};

// control that shows state info on hover
var label = new L.Label();
var info = L.control();
info.onAdd = function(map) {
  this._div = L.DomUtil.create('div', 'info');
  this.update();
  return this._div;
};

info.update = function(props) {
  this._div.innerHTML = (props ?
    '<h4>' + props.name + "[" + props.id + ']</h4>' + props.data + " " + '</sup>' :
    'เลื่อน mouse over แผนที่ <br/>');

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
  label.setContent("<h4>" + e.target.feature.properties.name + "</h4>" + e.target.feature.properties.data);
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
  id = e.target.feature.properties.id;
  type = (e.target.feature.properties.type * 1) + 1;
  if (type < 4) {
    var _0xc4ea = ["\x67\x65\x74\x42\x6F\x75\x6E\x64\x73", "\x74\x61\x72\x67\x65\x74",
      "\x66\x69\x74\x42\x6F\x75\x6E\x64\x73", "\x67\x65\x74\x43\x65\x6E\x74\x65\x72",
      "\x3C\x64\x69\x76\x3E\x20\x3C\x69\x6D\x67\x20\x73\x72\x63\x3D\x22\x69\x6D\x67\x2F\x62\x67\x2D\x73\x70\x69\x6E\x6E\x65\x72\x2E\x67\x69\x66\x22\x20\x61\x6C\x74\x3D\x22\x4C\x6F\x61\x64\x69\x6E\x67\x22\x20\x68\x65\x69\x67\x68\x74\x3D\x22\x34\x32\x22\x20\x77\x69\x64\x74\x68\x3D\x22\x34\x32\x22\x3E\x20\x3C\x2F\x64\x69\x76\x3E\x3C\x64\x69\x76\x20\x73\x74\x79\x6C\x65\x3D\x22\x63\x6F\x6C\x6F\x72\x3A\x62\x6C\x75\x65\x3B\x22\x20\x3E\x4C\x6F\x61\x64\x69\x6E\x67\x3C\x2F\x64\x69\x76",
      "\x61\x64\x64\x4C\x61\x79\x65\x72"
    ];
    map[_0xc4ea[2]](e[_0xc4ea[1]][_0xc4ea[0]]());
    var labelLocation = e[_0xc4ea[1]][_0xc4ea[0]]()[_0xc4ea[3]]();
    labelLoading = new L.LabelOverlay(labelLocation, _0xc4ea[4]);
    map[_0xc4ea[5]](labelLoading);
    //map    
    if (type == "2") {
      if (typeof pjson === "object") {
        map.removeLayer(pjson);
      }
      $.ajax({
        dataType: "json",
        url: 'geojson.php?a=' + id + '&t=2&r=covidmap',
        success: function(data) {
          var _0x2690 = ["\x62\x72\x69\x6E\x67\x54\x6F\x46\x72\x6F\x6E\x74", "\x61\x64\x64\x54\x6F",
            "\x67\x65\x6F\x4A\x73\x6F\x6E", "\x72\x65\x6D\x6F\x76\x65\x4C\x61\x79\x65\x72",
            "\x67\x65\x74\x5A\x6F\x6F\x6D"
          ];
          pjson = L[_0x2690[2]](data, {
            style: style,
            onEachFeature: onEachFeature
          })[_0x2690[1]](map)[_0x2690[0]]();
          map[_0x2690[3]](geojson);
          zoomlevel = map[_0x2690[4]]();
          map[_0x2690[3]](labelLoading);
        }
      }).error(function() {});

    }
    if (type == "3") {
      zone = e.target.feature.properties.zone;
      if (typeof pjson === "object") {
        map.removeLayer(pjson);
      }
      if (typeof ampjson === "object") {
        map.removeLayer(ampjson);
      }
      $.ajax({
        dataType: "json",
        url: 'geojson.php?a=' + id + '&t=3&r=covidmap',
        success: function(data) {
          var _0xbc81 = ["\x62\x72\x69\x6E\x67\x54\x6F\x46\x72\x6F\x6E\x74", "\x61\x64\x64\x54\x6F",
            "\x67\x65\x6F\x4A\x73\x6F\x6E", "\x72\x65\x6D\x6F\x76\x65\x4C\x61\x79\x65\x72",
            "\x67\x65\x74\x5A\x6F\x6F\x6D"
          ];
          ampjson = L[_0xbc81[2]](data, {
            style: style,
            onEachFeature: onEachFeature
          })[_0xbc81[1]](map)[_0xbc81[0]]();
          map[_0xbc81[3]](geojson);
          zoomlevel = map[_0xbc81[4]]();
          map[_0xbc81[3]](labelLoading);
        }
      }).error(function() {});
    } else {
      map.removeLayer(labelLoading);
    }
    //hospital layer
    if (type == "4") {
      if (typeof pjson === "object") {
        map.removeLayer(pjson);
      }
      if (typeof ampjson === "object") {
        map.removeLayer(ampjson);
        ampjson.setStyle({
          fillColor: ""
        });
      }
      $.ajax({
        dataType: "json",
        url: 'geojson.php?a=' + id + '&t=4&r=covidmap&year=<?php echo $gis_year; ?>',
        success: function(data) {
          var _0x6192 = ["\x62\x72\x69\x6E\x67\x54\x6F\x46\x72\x6F\x6E\x74", "\x61\x64\x64\x54\x6F",
            "\x63\x69\x72\x63\x6C\x65\x4D\x61\x72\x6B\x65\x72", "\x67\x65\x6F\x4A\x73\x6F\x6E",
            "\x72\x65\x6D\x6F\x76\x65\x4C\x61\x79\x65\x72", "\x67\x65\x74\x5A\x6F\x6F\x6D"
          ];
          hjson = L[_0x6192[3]](data, {
            style: style,
            pointToLayer: function(_0xba7fx1, _0xba7fx2) {
              return L[_0x6192[2]](_0xba7fx2, {
                radius: 10,
                fillOpacity: 0.85
              })
            },
            onEachFeature: onEachFeature
          })[_0x6192[1]](map)[_0x6192[0]]();
          map[_0x6192[4]](geojson);
          zoomlevel = map[_0x6192[5]]();
          map[_0x6192[4]](labelLoading);
        }
      }).error(function() {});

    } else {
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
var urlgo = 'geojson.php?a=&s=2&t=2&r=covidmap';
$.ajax({
  dataType: "json",
  url: urlgo,
  data: {
    d: <?php echo $data_json; ?>
  },
  success: function(data) {
    geojson = L.geoJson(data, {
      style: style,
      onEachFeature: onEachFeature
    }).addTo(map);

    map.fitBounds(geojson.getBounds());
  }
});

map.attributionControl.addAttribution('HDC &copy; <a href="https://www.moph.go.th/">กระทรวงสาธารณสุข</a>');
var legend = L.control({
  position: 'bottomright'
});

legend.onAdd = function(map) {
  var div = L.DomUtil.create('div', 'info legend');
  var labels = []; <?php echo $setlegend; ?>
  div.innerHTML = labels.join('<br>');
  return div;
};

legend.addTo(map);
//
var _0x7f65 = ["\x74\x6F\x70\x6C\x65\x66\x74", "\x63\x6F\x6E\x74\x72\x6F\x6C", "\x6F\x6E\x41\x64\x64", "\x64\x69\x76",
  "", "\x63\x72\x65\x61\x74\x65", "\x44\x6F\x6D\x55\x74\x69\x6C", "\x69\x6E\x6E\x65\x72\x48\x54\x4D\x4C",
  "\x3C\x64\x69\x76\x3E\x3C\x62\x75\x74\x74\x6F\x6E\x20\x69\x64\x3D\x27\x62\x74\x6E\x5F\x62\x61\x63\x6B\x27\x3E\u0E22\u0E49\u0E2D\u0E19\u0E01\u0E25\u0E31\u0E1A\x3C\x2F\x62\x75\x74\x74\x6F\x6E\x3E\x3C\x2F\x64\x69\x76\x3E",
  "\x61\x64\x64\x54\x6F"
];
var layerControl = L[_0x7f65[1]]({
  position: _0x7f65[0]
});
layerControl[_0x7f65[2]] = function(_0x96f8x2) {
  var _0x96f8x3 = L[_0x7f65[6]][_0x7f65[5]](_0x7f65[3], _0x7f65[4]);
  _0x96f8x3[_0x7f65[7]] = _0x7f65[8];
  return _0x96f8x3;
};
layerControl[_0x7f65[9]](map);
//
map.on("zoomend", function(e) {
  if (map.getZoom() === 6) {
    if (typeof ampjson === "object") {
      map.removeLayer(ampjson);
    }
    if (typeof pjson === "object") {
      map.removeLayer(pjson);
    }
  }
});

var _0xb64a = ["\x63\x6C\x69\x63\x6B", "\x61\x64\x64\x45\x76\x65\x6E\x74\x4C\x69\x73\x74\x65\x6E\x65\x72",
  "\x62\x74\x6E\x5F\x62\x61\x63\x6B", "\x67\x65\x74\x45\x6C\x65\x6D\x65\x6E\x74\x42\x79\x49\x64"
];
document[_0xb64a[3]](_0xb64a[2])[_0xb64a[1]](_0xb64a[0], function() {
  goBack()
});

function goBack() {
  if (typeof hjson === "object") {
    map.removeLayer(hjson);
    hjson = "";
    if (typeof ampjson === "object") {
      ampjson.setStyle(style);
      map.addLayer(ampjson);
      map.fitBounds(ampjson.getBounds());

      type = type - 1;
      if (type == "3") {
        var p = id.substring(0, 2);
      }
    }
  } else if (typeof ampjson === "object") {
    map.removeLayer(ampjson);
    if (typeof pjson === "object") {
      ampjson = "";
      map.addLayer(pjson);
      map.fitBounds(pjson.getBounds());
      type = type - 1;
      if (type == "3") {
        var p = id.substring(0, 2);
      }
    } else {
      map.removeLayer(ampjson);
      map.addLayer(geojson);
      map.fitBounds(geojson.getBounds());
    }
  } else {
    if (typeof pjson === "object") {
      map.removeLayer(pjson);
    }
    map.addLayer(geojson);
    map.fitBounds(geojson.getBounds());
  }
}
/////////////////////////////////////////////////////////////map
</script>

</html>