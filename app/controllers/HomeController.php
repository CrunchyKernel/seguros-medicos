<?php

class HomeController extends BaseController {
	protected $layout = 'layout.master';
	
	public function main()
	{
		$this->layout->content = View::make('main');
	}

}