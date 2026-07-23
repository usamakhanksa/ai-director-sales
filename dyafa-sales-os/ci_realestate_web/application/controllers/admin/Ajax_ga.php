<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . 'third_party/vendor/autoload.php';



class Ajax_ga extends MY_Controller
{

	var $analytics;
	var $view_id;
	var $dateRange;
	function __construct()
	{
		parent::__construct();
		$user_type = $this->session->userdata('user_type');
		/*if (!$this->isLogin() || $user_type != 'admin') {


			redirect('/logins', 'location');
		}*/
	}

	public function initializeAnalytics()
	{
		$CI = &get_instance();
		$this->json = $CI->config->item('ga_analytics_json');
		$this->view_id = $CI->config->item('ga_view_id');

		$KEY_FILE_LOCATION = APPPATH . 'config/' . $this->json;

		// Create and configure a new client object.
		$client = new Google_Client();
		$client->setApplicationName("Hello Analytics Reporting");
		$client->setAuthConfig($KEY_FILE_LOCATION);
		$client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
		
		$this->analytics = new Google_Service_AnalyticsReporting($client);
		//$this->view_id = "212281600";

	}

	public function set_date_range()
	{

		//$this->initializeAnalytics();
		$this->dateRange = new Google_Service_AnalyticsReporting_DateRange();
		$this->dateRange->setStartDate("10daysAgo");
		$this->dateRange->setEndDate("today");
	}

	public function show_ga_data()
	{
		if (!$this->analytics) {
			$this->initializeAnalytics();
			$this->set_date_range();
		}
		$dataArr =  array();

		$dataArr['visitors'] = array(
			'datatype' => 'ga:visitors',
			'title' => 'Visitors',
		);

		$dataArr['sessions'] = array(
			'datatype' => 'ga:sessions',
			'title' => 'Sessions',
		);

		$dataArr['page_views'] = array(
			'datatype' => 'ga:pageviews',
			'title' => 'Page Views',
		);

		$dataArr['bounce_rate'] = array(
			'datatype' => 'ga:bounceRate',
			'title' => 'Bounce Rate',
		);

		$dataArr['new_visitors'] = array(
			'datatype' => 'ga:newUsers',
			'title' => 'New Visitors',
		);

		/*$dataArr ['session_duration'] = array( 'datatype' => 'ga:sessionDuration',
									'title' => 'Session Duration',);*/

		$dataArr['pages_per_session'] = array(
			'datatype' => 'ga:pageviewsPerSession',
			'title' => 'pageviewsPerSession',
		);


		$dataArr['avg_session_duration'] = array(
			'datatype' => 'ga:avgSessionDuration',
			'title' => 'Avg Session Duration',
		);
		$dataArr['session_per_user'] = array(
			'datatype' => 'ga:sessionsPerUser',
			'title' => 'Session Per User',
		);

		$response = $this->getAnalyticsDataMulti($dataArr);

		$output = $this->getResults($response);
		echo  json_encode($output);
	}

	public function show_ga_map()
	{

		if (!$this->analytics) {
			$this->initializeAnalytics();
			$this->set_date_range();
		}
		
		/*$this->dateRange = new Google_Service_AnalyticsReporting_DateRange();
		$this->dateRange->setStartDate("10daysAgo");
		$this->dateRange->setEndDate("today");
		*/
		//$dateRange = new Google_Service_AnalyticsReporting_DateRange();
		//$dateRange->setStartDate($tstartDate);
		//$dateRange->setEndDate($tendDate);
		
		
		$dataArr =  array();

		$dataDimArr =  array();
		$dataDimArr['dim_map'] = array(
			'datatype' => 'ga:countryIsoCode',
			'title' => 'Map',
		);

		$response = $this->getAnalyticsDataMulti($dataArr, $dataDimArr);

		$output = $this->getMap($response);
		echo  json_encode($output);
	}

	public function show_ga_hits()
	{

		if (!$this->analytics) {
			$this->initializeAnalytics();
			$this->set_date_range();
		}
		$dataArr =  array();

		$dataArr['hits'] = array(
			'datatype' => 'ga:hits',
			'title' => 'Hits',
		);

		/*$dataArr ['session'] = array( 'datatype' => 'ga:sessions',
										'title' => 'session',);  */


		$dataDimArr =  array();

		/*$dataDimArr ['users'] = array( 'datatype' => 'ga:sessionCount',
										'title' => 'Users',); */

		$dataDimArr['browser'] = array(
			'datatype' => 'ga:browser',
			'title' => 'Browser',
		);

		$dataDimArr['deviceCategory'] = array(
			'datatype' => 'ga:deviceCategory',
			'title' => 'deviceCategory',
		);

		$dataDimArr['operatingSystem'] = array(
			'datatype' => 'ga:operatingSystem',
			'title' => 'OS',
		);


		$response = $this->getAnalyticsDataMulti($dataArr, $dataDimArr);
		$output = $this->getResultMetrics($response);
		echo  json_encode($output);
	}

	public function show_ga_users_per_day()
	{
		if (!$this->analytics) {
			$this->initializeAnalytics();
			$this->set_date_range();
		}

		$dataArr =  array();

		$dataArr['users'] = array(
			'datatype' => 'ga:users',
			'title' => 'Users',
		);

		$dataArr['newUsers'] = array(
			'datatype' => 'ga:newUsers',
			'title' => 'newUsers',
		);


		$dataDimArr =  array();

		/*$dataDimArr ['ga:pagePath'] = array( 'datatype' => 'ga:pagePath',
										'title' => 'ga:pagePath',); */

		$dataDimArr['date'] = array(
			'datatype' => 'ga:date',
			'title' => 'date',
		);



		$response = $this->getAnalyticsDataMulti($dataArr, $dataDimArr);




		/****************************************************/


		/*echo '<pre>';
		  $this->printResults($response);
		exit; */
		$output = $this->getResultMetrics($response);
		echo  json_encode($output);
	}


	public function show_ga_top_pages()
	{
		if (!$this->analytics) {
			$this->initializeAnalytics();
			$this->set_date_range();
		}

		$dataArr =  array();


		/*********************************************************/
		/**	https://www.sanwebe.com/2013/05/top-viewed-pages-with-google-analytics-api */
		/*********************************************************/
		$dataArr =  array();
		$dataArr['PageViews'] = array(
			'datatype' => 'ga:pageviews',
			'title' => 'PageViews',
		);
		$dataDimArr =  array();
		$dataDimArr['PagePath'] = array(
			'datatype' => 'ga:landingPagePath',
			'title' => 'Page',
		);

		$dataDimArr['pageTitle'] = array(
			'datatype' => 'ga:pageTitle',
			'title' => 'Page Title',
		);

		$response = $this->getAnalyticsDataMulti($dataArr, $dataDimArr);

		/***
		Pageviews	Unique Pageviews	Entrances	Avg. Time On Page	Bounce Rate	% Exits
		11			11					2			00:00:54			0.0%			27.27%
		 **/
		/****************************************************/


		//echo '<pre>';
		//$this->printResults($response);
		//exit;  
		/*$output = $this->getMap($response);
		echo  json_encode($output);*/
		$output = $this->getResultMetrics($response);
		echo  json_encode($output);
	}

	public function show_ga_top_referrals()
	{
		if (!$this->analytics) {
			$this->initializeAnalytics();
			$this->set_date_range();
		}
		$dataArr =  array();

		$dataArr['map'] = array(
			'datatype' => 'ga:fullReferrer',
			'title' => 'Reffrals',
		);

		$response = $this->getAnalyticsDataReferrals();

		echo '<pre>';
		$this->printResults($response);
		exit;
		/*$output = $this->getMap($response);
		echo  json_encode($output);*/
	}

	public function  getAnalyticsDataReferrals()
	{

		// Create the Metrics object.
		$m1 = new Google_Service_AnalyticsReporting_Metric();
		$m1->setExpression("ga:users");
		$m1->setAlias("users");
		/*
		$m2 = new Google_Service_AnalyticsReporting_Metric();
		$m2->setExpression("ga:session");
		$m2->setAlias("session");
		*/

		$referralPath = new Google_Service_AnalyticsReporting_Dimension();
		$referralPath->setName("ga:fullReferrer");
		//$referralPath->setName("ga:source");
		// Create the ReportRequest object.
		$request = new Google_Service_AnalyticsReporting_ReportRequest();
		$request->setViewId($this->view_id);
		$request->setDimensions($referralPath);


		/*echo "<pre>";
		print_r($request);
		echo "</pre>";*/
		$body = new Google_Service_AnalyticsReporting_GetReportsRequest();
		$body->setReportRequests(array($request));
		return $this->analytics->reports->batchGet($body);
	}


	public function show_ga_browser_per_session()
	{
		if (!$this->analytics) {
			$this->initializeAnalytics();
			$this->set_date_range();
		}
		$dataArr =  array();
		$dataArr['sessions'] = array(
			'datatype' => 'ga:sessions',
			'title' => 'Sessions',
		);
		$dataDimArr =  array();
		$dataDimArr['browser'] = array(
			'datatype' => 'ga:browser',
			'title' => 'Browser',
		);

		$response = $this->getAnalyticsDataMulti($dataArr, $dataDimArr);
		$output = $this->getMap($response);
		echo  json_encode($output);
	}



	public function getAnalyticsDataMulti($dataArr = array(), $dataDimArr = array())
	{

		$metrics = array();
		if (count($dataArr) > 0) {
			foreach ($dataArr as $dataKey => $data) {
				// Create the Metrics object.
				$$dataKey = new Google_Service_AnalyticsReporting_Metric();
				$$dataKey->setExpression($data['datatype']);
				$$dataKey->setAlias($data['title']);
				$metrics[] = $$dataKey;
			}
		}

		$dimensions = array();
		if (count($dataDimArr) > 0) {
			foreach ($dataDimArr as $dataKey => $data) {
				$$dataKey = new Google_Service_AnalyticsReporting_Dimension();
				$$dataKey->setName($data['datatype']);
				$dimensions[] = $$dataKey;
			}
		}

		// Create the ReportRequest object.
		$request = new Google_Service_AnalyticsReporting_ReportRequest();
		$request->setViewId($this->view_id);
		$request->setDateRanges($this->dateRange);

		if (count($metrics) > 0) {
			$request->setMetrics($metrics);
		}
		if (count($dimensions) > 0) {
			$request->setDimensions($dimensions);
		}
		/*echo "<pre>";
		print_r($request);
		echo "</pre>";*/
		$body = new Google_Service_AnalyticsReporting_GetReportsRequest();
		$body->setReportRequests(array($request));
		return $this->analytics->reports->batchGet($body);
	}


	public function getResults($reports)
	{

		$output = array();

		for ($reportIndex = 0; $reportIndex < count($reports); $reportIndex++) {
			$report = $reports[$reportIndex];
			$header = $report->getColumnHeader();
			$dimensionHeaders = $header->getDimensions();
			$metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
			$rows = $report->getData()->getRows();
			
			if(!is_array($dimensionHeaders)) $dimensionHeaders = array();
			

			for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
				$row = $rows[$rowIndex];
				$dimensions = $row->getDimensions();
				if(!is_array($dimensions)) $dimensions = array();
				$metrics = $row->getMetrics();
				for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
					print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
				}

				//print_r($metrics);
				//print_r($metricHeaders);
				for ($j = 0; $j < count($metrics); $j++) {
					$values = $metrics[$j]->getValues();
					for ($k = 0; $k < count($values); $k++) {
						$entry = $metricHeaders[$k];
						//print($entry->getName() . ": " . $values[$k] . "\n");
						//echo $entry->type;
						$name = $entry->getName();
						$name = strtolower(str_replace(" ", "_", $name));

						$value = $values[$k];

						if ($entry->type == 'TIME') {
							$value = round($value);
							$value = gmdate("H:i:s", $value);
						}
						if ($entry->type == 'FLOAT') {
							$value = sprintf("%.2f", $value);
						}
						if ($entry->type == 'PERCENT') {
							$value = sprintf("%.2f", $value) . "%";
						}
						$output[$name] = $value;
					}
				}
			}
		}

		return $output;
	}

	public function getResultMetrics($reports)
	{

		$output = array();

		for ($reportIndex = 0; $reportIndex < count($reports); $reportIndex++) {
			$report = $reports[$reportIndex];
			$header = $report->getColumnHeader();
			$dimensionHeaders = $header->getDimensions();
			$metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
			$rows = $report->getData()->getRows();
			$metric_row = array();

			for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
				$row = $rows[$rowIndex];
				$dimensions = $row->getDimensions();
				$metrics = $row->getMetrics();
				for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
					/*print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");*/
					$dimHeader = $dimensionHeaders[$i];
					$dimHeader = str_replace("ga:", "", $dimHeader);

					$value = $dimensions[$i];
					if ($dimHeader == "date") {
						$value = substr($value, 0, 4) . "-" . substr($value, 4, 2) . "-" . substr($value, 6, 2);
					}
					$metric_row[$dimHeader] = $value;
				}

				//print_r($metrics);
				//print_r($metricHeaders);
				for ($j = 0; $j < count($metrics); $j++) {
					$values = $metrics[$j]->getValues();
					for ($k = 0; $k < count($values); $k++) {
						$entry = $metricHeaders[$k];
						//print($entry->getName() . ": " . $values[$k] . "\n");
						//echo $entry->type;
						$name = $entry->getName();
						$name = strtolower(str_replace(" ", "_", $name));

						$value = $values[$k];

						if ($entry->type == 'TIME') {
							$value = round($value);
							$value = gmdate("H:i:s", $value);
						}
						if ($entry->type == 'FLOAT') {
							$value = sprintf("%.2f", $value);
						}
						if ($entry->type == 'PERCENT') {
							$value = sprintf("%.2f", $value) . "%";
						}


						//$output [$name ] = $value;
						$metric_row[$name] = $value;
					}
				}

				$output[] =  $metric_row;
			}
		}

		return $output;
	}


	public function getMap($reports)
	{

		$output = array();
		for ($reportIndex = 0; $reportIndex < count($reports); $reportIndex++) {
			$report = $reports[$reportIndex];
			$header = $report->getColumnHeader();
			$dimensionHeaders = $header->getDimensions();
			$metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
			$rows = $report->getData()->getRows();

			for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
				$row = $rows[$rowIndex];
				$dimensions = $row->getDimensions();
				$metrics = $row->getMetrics();
				for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {

					/*$output [] = $dimensionHeaders[$i] . ": " . $dimensions[$i] ;*/
				}

				for ($j = 0; $j < count($metrics); $j++) {
					$values = $metrics[$j]->getValues();
					for ($k = 0; $k < count($values); $k++) {
						$entry = $metricHeaders[$k];

						/*$output [] = $entry->getName() . ": " . $values[$k] ;*/
						$output[$dimensions[$j]] = $values[$k];
					}
				}
			}
		}

		return $output;
	}


	public function printResults($reports)
	{

		for ($reportIndex = 0; $reportIndex < count($reports); $reportIndex++) {
			$report = $reports[$reportIndex];
			$header = $report->getColumnHeader();
			$dimensionHeaders = $header->getDimensions();
			$metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
			$rows = $report->getData()->getRows();

			for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
				$row = $rows[$rowIndex];
				$dimensions = $row->getDimensions();
				$metrics = $row->getMetrics();
				for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
					print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
				}

				for ($j = 0; $j < count($metrics); $j++) {
					$values = $metrics[$j]->getValues();
					for ($k = 0; $k < count($values); $k++) {
						$entry = $metricHeaders[$k];
						print($entry->getName() . ": " . $values[$k] . "\n");
					}
				}
			}
		}
	}
}
