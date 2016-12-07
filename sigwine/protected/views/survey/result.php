<?php
/* @var $this SurveyController */
/* @var $model Survey */

$this->breadcrumbs = array(
    'Surveys' => array('index'),
    $model->survey_id,
);

$this->menu = array(
    /* array('label'=>'List Survey', 'url'=>array('index')), 
     */
      array('label'=>'Create Survey', 'url'=>array('create')),
      array('label'=>'Update Survey', 'url'=>array('update', 'id'=>$model->survey_id)),
      array('label'=>'Delete Survey', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->survey_id),'confirm'=>'Are you sure you want to delete this item?')),
     
    array('label' => 'Manage Survey', 'url' => array('admin')),
);
?>

<h1>View Survey Result #<?php echo $model->survey_id; ?></h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'survey_id',
        'survey_name',
        'survey_start',
        'survey_end',
        'status',
        'created_on',
    ),
));
?>
<?php
$survey_id = $model->survey_id;
$wb = new Webservice();
$query = "SELECT * FROM survey_question where survey_id='" . $survey_id . "'";
$res = $wb->getAllData($query);
$questionArr = array();
$queRe = array();

if ($res) {
    $totalRows_rs_vote = 1;
    foreach ($res as $rk => $rv) {

        $questionArr[] = $rv["survey_question_id"];
        $query_rs_vote = "select * from survey_result where survey_question_id ='" . $rv["survey_question_id"] . "' and survey_option_id!='-1'";
        $totalRows_rs_vote = $wb->getCount($query_rs_vote);
        $query1 = "SELECT * FROM survey_option where survey_id='" . $survey_id . "' and survey_question_id='" . $rv["survey_question_id"] . "'";
        $resop = $wb->getAllData($query1);
        //echo "<br/>";
        // $num_rowsQuestion1 = $wb->getCount("SELECT * FROM survey_result WHERE survey_question_id='".$rv["survey_question_id"]."' and survey_option_id='-1'");
        // $arr[$rv["survey_question_id"]][0]= ($num_rowsQuestion1 / $totalRows_rs_vote) * 100;
        $arr[$rv["survey_question_id"]] = array("question" => $rv["question"], "total" => $totalRows_rs_vote);
        foreach ($resop as $ap => $ak) {
            $num_rowsQuestion1 = $wb->getCount("SELECT * FROM survey_result WHERE survey_question_id='" . $rv["survey_question_id"] . "' and survey_option_id='" . $ak["survey_option_id"] . "'");
            if ($totalRows_rs_vote > 0) {
                $percentQuestion1 = ($num_rowsQuestion1 / $totalRows_rs_vote) * 100;
            }
	    else
	    {
		$percentQuestion1 = '0';
	    }
            $arr[$rv["survey_question_id"]]['option'][$ak["survey_option_id"]] = array("option" => $ak["option"], "per" => $percentQuestion1);
        }
    }
    //  echo "<pre>";
    //  print_r($arr);
    // die;
}
?>
<table id="yw0" class="detail-view">
        <tbody>
        <tr class="odd"><th colspan="2" style="text-align: left !important;padding-left:90px;">Survey Result</th></tr>
        <tr class="even"><th>Survey Name</th><td><?php echo $model->survey_name; ?></td></tr>
        <tr class="odd"><th>&nbsp;</th><td>
                <ul>
                    <?php
                    $i = 1;
                    foreach ($arr as $surk => $surAns) {
                        ?>
                            <?php if ($surAns["total"] > 0) { ?> 
                            <li>
                                <span class="total-votes"><?php echo $i++ ?>&nbsp;&nbsp;<strong><?php echo $surAns["question"] ?></strong></span> 
                                <br><i>Total Votes: <?php echo $surAns["total"] ?></i><br />
                                    <?php foreach ($surAns["option"] as $k => $v) { ?>
                                    <strong><?php echo $v["option"]; ?></strong>:<br />
                                    <div class="results-bar" style="width: <?php echo round($v["per"], 2); ?>%;">
            <?php echo round($v["per"], 2); ?>%
                                    </div>
                            <?php } ?>
                                <br/>
                            </li>
                            <?php } else { ?>
                            <li>
                                <span class="total-votes"><?php echo $i++ ?>&nbsp;&nbsp;<strong><?php echo $surAns["question"] ?></strong></span> 
                                <br><i>Total Votes: <?php echo $surAns["total"] ?></i><br />
                                <?php foreach ($surAns["option"] as $k => $v) { ?>
                                    <strong><?php echo $v["option"]; ?></strong>:<br />
                                    <div>No Result Found!</div>
                            <?php } ?>
                                <br/></li>
    <?php } ?>
<?php } ?>
                </ul></td></tr>
        <tr class="even">
            <td colspan="2" align="center" class="but">
                <input type="button" onclick="window.location.href = '<?php echo Yii::app()->request->baseUrl; ?>/index.php/survey/admin';" value="Go to Survey">
            </td>
        </tr>
    </tbody></table>
<style type="text/css">
    ul { list-style: none; margin-bottom: 15px;}
    .results-bar {
        padding: 5px;
        color: white;
        background: url(<?php echo Yii::app()->request->baseUrl; ?>/images/result-bar-bg.png) left center;
        white-space: nowrap;
    }
    .but{
        text-align:center;
    }
    .but input[type="button"] {
        background: #eb8e72 none repeat scroll 0 0;
        border: medium none !important;
        color: #fff;
        cursor: pointer;
        padding: 7px 15px;
    }
</style>