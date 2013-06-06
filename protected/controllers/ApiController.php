<?php

class ApiController extends Controller
{

	public function actionGetBlock($id){
		header('Content-type: application/json');

		$result = (object) array('result' => 0, 'error' => '', 'response' => '');

		if(is_numeric($id) && $id > 0){
			$model = Block::model()->findByPk($id);
			if($model !== null){
				$data = array('price' => $model->price);
				
				$data['images'] = array();
				if($model->images){
					foreach ($model->images as $key => $item) {
						$data['images'][$key]['display'] = '/uploads/'.$model->id.'/'.$item->filename;
						$data['images'][$key]['retina_display'] = '/uploads/'.$model->id.'/retina/'.$item->filename;
					} 
				}
				$result->result = 1; 
				$result->response = (object) $data;
			}else{
				$result->error = "Not found";
			}
		}

		echo CJavaScript::jsonEncode($result);
		Yii::app()->end();
	}

	public function actionAllBlocks(){
		header('Content-type: application/json');

		$result = (object) array('result' => 0, 'error' => '', 'response' => '');

		$blocks = Block::model()->findAll(array('order' => 'sort', 'condition' => 'public != 0 AND preview != :img', 'params'=>array(':img' => '')));

		if($blocks !== null){
			$data['blocks'] = array();
			foreach ($blocks as $key => $item) {
				//if(empty($item->preview) || $item->public == 0) continue;
				$data['blocks'][$key]['id'] = $item->id;
				$data['blocks'][$key]['name'] = $item->name;
				$data['blocks'][$key]['desc'] = $item->desc;
				$data['blocks'][$key]['price'] = $item->price;
				$data['blocks'][$key]['images']['display'] = '/uploads/'.$item->preview;
				$data['blocks'][$key]['images']['retina_display'] = '/uploads/retina/'.$item->preview;
			}
			$result->result = 1; 
			$result->response = (object) $data;
		}else{
			$result->error = "Not found";
		}

		echo CJavaScript::jsonEncode($result);
		Yii::app()->end();
	}

	public function actionInfo(){
		header('Content-type: application/json');
		$model = Page::model()->findByPk(1);
		$result = (object) array('response' => '');
		$result->response = $model->content;
		echo CJavaScript::jsonEncode($result);
		Yii::app()->end();
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