<?php

class FrontController extends Controller
{
	public function filters(){
		return array(
			array('application.modules.auth.filters.AuthFilter'),
		);
	}
}
?>