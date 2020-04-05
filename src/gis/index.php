<?php
require('./../../config/config.inc.php');
require_once('./../../includes/connectDB.php');
require('./../../includes/function.ini.php');
require("./../lib.inc.php");

$vars = array_merge($_GET, $_POST);
array_walk_recursive($vars, function (&$item, $key) {
    $item = addslashes($item);
});
headvar($vars);
extract($vars_clear);

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
if($config_level==1){
    $groupby="zonecode";
}else if($config_level==2){
    $groupby="cc";
}else if($config_level==3){
    $groupby="ccaa";
}

$sql2="SELECT report_name,source_table,target as kpi,rate,notice,budgetyear FROM sys_report WHERE id='".$reportid."' ";
//echo $sql2;
try {
    $query2 = $db->prepare($sql2);
    $query2->execute();
    $rows_data2 = $query2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}
extract($rows_data2[0]);

$sql3="SELECT option_code,option_name,sql_gis,color as jsoncolor 
        FROM sys_gis WHERE id='".$reportid."' AND active=1 ORDER BY option_code ASC ";
//echo $sql3;
try {
    $query3 = $db->prepare($sql3);
    $query3->execute();
    $rows_data3 = $query3->fetchAll(PDO::FETCH_ASSOC);
    $rcount= $query3->rowCount();
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}
 if($budgetyear=="0"){$showbudgetyear=" ปี พ.ศ. ";}else{$showbudgetyear=" ปีงบประมาณ ";}
?>
<div class="row" >
     <div class="col-md-12">
    	<div class="box" style="margin-top: 10px;" >
	    	<div class="box-title" style="text-align:center;">
            <h4><i class="glyphicon glyphicon-globe"></i>
                        <?php echo $report_name."<br>".$report_area .$showbudgetyear.$byear."<br>";?></h4>
            </div>
            <div class="box-content">
                <?php if($rcount > 1){?>
                         <form>
                            <input type="hidden" id="reportid" name="reportid" value="<?php echo $reportid;?>" / >
                            <input type="hidden" id="byear" name="byear" value="<?php echo $byear;?>" / >
                            <input type="hidden" id="selAllProv" name="selAllProv" value="<?php echo $selAllProv;?>" / >
                            <select id="option_gis" name="option_gis" class="form-control" onchange="onChangegis(this.value);">
                            <?php
                            foreach($rows_data3 as $s=>$t){
                                //if($s==$option_gis){$ss="selected";}else{$ss="";} 
                                echo "<option value=".$s." ".$ss.">".$rows_data3[$s]["option_name"]."</option>";
                            } 
                            ?>
                            </select>
                        </form>
                    <?php } ?>
            </div>
            </div>
        </div>
    </div>
    <div class="row" >
    <div id="gis" style="margin: 0px 40px 0px 40px;"></div>
    </div>

 <script>
        $(document).ready(function () {
        onload($("#option_gis").val());
        function onload(e){
            urlgis = 'gis/gis.php?reportid=<?php echo $reportid;?>&selAllProv=<?php echo $selAllProv;?>&byear=<?php echo $byear; ?>&option_gis='+e
            $.ajax({
            type: 'GET',
            url: urlgis,
            success: function(data) {
                $('#gis').html(data);
            }
            }).error(function() {});
        }
        });
        function onChangegis(e){
            urlgis1 = 'gis/gis.php?reportid=<?php echo $reportid;?>&selAllProv=<?php echo $selAllProv;?>&byear=<?php echo $byear; ?>&option_gis='+e
            $.ajax({
            type: 'GET',
            url: urlgis1,
            success: function(data) {
                $('#gis').html(data);
            }
            }).error(function() {});
        }

    </script>
