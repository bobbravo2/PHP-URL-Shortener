<?php 
require_once 'config.php';
require_once 'includes.php';
noCacheHeaders();
userIsAuthorized();
?>
<!DOCTYPE html>
<html>
<head>
<title>Analytics</title>
<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="css/style.css" />
<meta name = "viewport" content = "width = device-width">
</head>
<body>
<?php 
if (isset($_REQUEST['id'])) {
	//todo
	//get first click day, then get last click day
	//do a for loop, building up the total number of clicks for each day
	//then graph that in timeline
	$clicks = get_clicks($_REQUEST['id']);
	$first_click = reset($clicks);
	$last_click = end($clicks);
	$first_click_ts = strtotime($first_click['time']);
	$last_click_ts = strtotime($last_click['time']);
	$first_click_days = $first_click_ts / (60 * 60 * 24 );
	$last_click_days  = $last_click_ts / (60 * 60 * 24);
	$time_period_elapsed = round($last_click_days - $first_click_days);
	$clicks_per_day = array();
	if (count($clicks) > 0 ) {
		$first_click_day_ts = strtotime(date('Y-m-d', $first_click_ts));
		$last_click_day_ts = strtotime(date('Y-m-d', $last_click_ts));
		for ($day = 0; $day <= $time_period_elapsed; $day++) {
			$current_date_ts = ($day * 60 *60 * 24) + $first_click_day_ts;
			$next_date_ts = (($day + 1) * 60 *60 * 24) + $first_click_day_ts;
			$current_date_string = date('Y-m-d', $current_date_ts);
			$total_day_clicks = 0;
			foreach ($clicks as $click) {
				$click_ts = strtotime($click['time']);
				if ($click_ts > $current_date_ts && $click_ts <= $next_date_ts) {
					$total_day_clicks++;
				} 
			}
			$clicks_per_day[$current_date_string] = $total_day_clicks;
		}
	}
	switch (count($clicks)) :
	case 0: ?>
		<div class="alert alert-info hero-unit">
		<blockquote>
			<h2>No conversions yet. Hang tight, I'm sure there will be soon!</h2>
		</blockquote>
			<p class="alert alert-success" >To try the process yourself, copy the short url, and paste it in the browser. Or, scan the code right from your phone! Then hit refresh.</p>
		</div>
	<?php 
		break;
	case 1: ?>
		<div class="alert alert-success hero-unit">
			<h2>Great Job!</h2>
			<p>One conversion so far on <?php echo key($clicks_per_day)?>.<br/> 
			<b>Once there are more, we'll display a nifty graph of clicks over time.</b></p>
		</div>
	<?php 
		break;
	default: ?>
    <?php if ($time_period_elapsed > 0 ) : //Only show timeline if there is more than 1 day elapsed?>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
		google.load('visualization', '1.0', {'packages':['annotatedtimeline']});
	</script>
	<script type="text/javascript">
	var t = new google.visualization.DataTable();
		t.addColumn('date', 'Date');
		t.addColumn('number', 'Clicks');
		t.addRows([
   			<?php foreach ($clicks_per_day as $date => $clicks ): ?>
				[<?php echo formatJSdate($date);?>,   <?php echo $clicks; ?>],
			<?php endforeach; ?>
		]);
	</script>
	<div id="visualization" style="height:400px; width:600px;" ></div>
    <script>
		google.setOnLoadCallback(function  () {
	  		var timeline = new google.visualization.AnnotatedTimeLine(document.getElementById('visualization'));
			timeline.draw(t, {'displayAnnotations': false});
		});
		</script>
		<?php else: //There are multiple clicks, but only 1 day to display ?>
			<div class="alert alert-success hero-unit">
				<h1>Great job!</h1>
				<p>There have been <?php echo count($clicks)?> conversions so far today. Once more time has elapsed, you'll be able to see the conversions over time.</p>
			</div>
		<?php endif;?>
	<?php endswitch; 
} else { ?>
<div class="alert alert-error">
	<p>Error. No ID. Please go back and select the ID you'd like to view the click statistics for.</p>';
</div>
<?php } //End id check?>
</body>
</html>