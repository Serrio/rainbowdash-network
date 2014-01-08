 <?php



if (!defined('STATUSNET') && !defined('LACONICA')) {

    exit(1);

}



class AdBannerPlugin extends Plugin{

	public $adsenseCode;



	 function onEndShowInsideFooter($action){

	 			$action->elementStart('div', array('id' => 'adbanner'));

    			$action->raw($this->adsenseCode);

    			$action->elementEnd('div');

     return true;

    	}

}


