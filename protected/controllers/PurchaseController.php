<?php

class PurchaseController extends Controller
{
	public function actionVerify(){
		header('Content-type: application/json');			

		if(!empty($_POST['receiptdata'])){
			$devmode = TRUE; // change this to FALSE after testing in sandbox

			$receiptdata = $_POST['receiptdata'];

			if($devmode)
				$appleURL = "https://sandbox.itunes.apple.com/verifyReceipt";
			else
				$appleURL = "https://buy.itunes.apple.com/verifyReceipt";

		 	$receipt = json_encode(array("receipt-data" => $receiptdata));
			$response_json = $this->doPostRequest($appleURL, $receipt);
			$response = json_decode($response_json);

			if($response->{'status'} == 0)
				echo CJavaScript::jsonEncode('Yes');
			else
				echo CJavaScript::jsonEncode('No');
		}else{
			echo CJavaScript::jsonEncode('No send data');
		}
		Yii::app()->end();
	}

	public function actionRequestAccess(){
		header('Content-type: application/json');

		if(isset($_POST['Requests'])){
			$model = new Requests;
			$model->attributes = $_POST['Requests'];
			if($model->save())
				echo CJavaScript::jsonEncode('Done');
			else
				echo CJavaScript::jsonEncode('Unable to insert');
		}
		else
			echo CJavaScript::jsonEncode('No send data');

		Yii::app()->end();
	}

	public function actionFeatureCheck($udid, $productid){
		header('Content-type: application/json');

		if(!empty($udid) && !empty($productid)){
			$req = Requests::model()->find('udid=:udid AND productid=:productid', array(':udid'=>$udid, ':productid'=>$productid));
			if($req)
				echo CJavaScript::jsonEncode('Yes');
			else
				echo CJavaScript::jsonEncode('No');
		}

		Yii::app()->end();
	}

	private function doPostRequest($url, $data, $optional_headers = null)
	{
	 	$params = array('http' => array(
          	'method' => 'POST',
          	'content' => $data
        ));
		
		if ($optional_headers !== null) {
			$params['http']['header'] = $optional_headers;
		}
		
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		
		if (!$fp) {
			throw new Exception("Problem with $url, $php_errormsg");
		}
		
		$response = @stream_get_contents($fp);
		if ($response === false) {
			throw new Exception("Problem reading data from $url, $php_errormsg");
		}
		return $response;
	}


	// public function actionVerify()
	// {
	// 	header('Content-type: application/json');
	// 	if(!empty($_POST['receipt'])){
	// 		$info = $this->getReceiptData($_POST['receipt']);
	// 		echo CJavaScript::jsonEncode($info);
	// 	}else{
	// 		echo CJavaScript::jsonEncode('No data');
	// 	}
	// 	Yii::app()->end();
	// }

	// private function getReceiptData($receipt, $isSandbox = true){
	// 	if ($isSandbox) {
 //            $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
 //        }
 //        else {
 //            $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
 //        }

 //        // build the post data
 //        $postData = json_encode(
 //            array('receipt-data' => $receipt)
 //        );

 //        // create the cURL request
 //        $ch = curl_init($endpoint);
 //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 //        curl_setopt($ch, CURLOPT_POST, true);
 //        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
 
 //        // execute the cURL request and fetch response data
 //        $response = curl_exec($ch);
 //        $errno    = curl_errno($ch);
 //        $errmsg   = curl_error($ch);
 //        curl_close($ch);

 //        // ensure the request succeeded
 //        if ($errno != 0) {
 //            throw new Exception($errmsg, $errno);
 //        }

 //        // parse the response data
 //        $data = json_decode($response);
 
 //        // ensure response data was a valid JSON string
 //        if (!is_object($data)) {
 //            throw new Exception('Invalid response data');
 //        }
 
 //        // ensure the expected data is present
 //        if (!isset($data->status) || $data->status != 0) {
 //            throw new Exception('Invalid receipt');
 //        }

 //        // build the response array with the returned data
 //        return $data;
	// }

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}