<?php

class PurchaseController extends Controller
{
	public function actionVerify()
	{
		header('Content-type: application/json');
		if(!empty($_POST['receipt'])){
			$info = $this->getReceiptData($_POST['receipt']);
			echo CJavaScript::jsonEncode($info);
		}else{
			echo CJavaScript::jsonEncode('No data');
		}
		Yii::app()->end();
	}

	private function getReceiptData($receipt, $isSandbox = true){
		if ($isSandbox) {
            $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
        }
        else {
            $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
        }

        // build the post data
        $postData = json_encode(
            array('receipt-data' => $receipt)
        );

        // create the cURL request
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
 
        // execute the cURL request and fetch response data
        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $errmsg   = curl_error($ch);
        curl_close($ch);

        // ensure the request succeeded
        if ($errno != 0) {
            throw new Exception($errmsg, $errno);
        }

        // parse the response data
        $data = json_decode($response);
 
        // ensure response data was a valid JSON string
        if (!is_object($data)) {
            throw new Exception('Invalid response data');
        }
 
        // ensure the expected data is present
        if (!isset($data->status) || $data->status != 0) {
            throw new Exception('Invalid receipt');
        }

        // build the response array with the returned data
        return array(
            'quantity'       =>  $data->receipt->quantity,
            'product_id'     =>  $data->receipt->product_id,
            'transaction_id' =>  $data->receipt->transaction_id,
            'purchase_date'  =>  $data->receipt->purchase_date,
            'app_item_id'    =>  $data->receipt->app_item_id,
            'bid'            =>  $data->receipt->bid,
            'bvrs'           =>  $data->receipt->bvrs
        );
	}

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