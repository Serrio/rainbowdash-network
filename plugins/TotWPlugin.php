<?php

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

class TotWPlugin extends Plugin {
	public $topicText = 'Topic of the Week';
	public $redirectLink = '/tag/totw';
	public $redirectText = 'Click here to tell us what you think';
	
	function onStartShowSiteNotice($action) {
        $text = common_config('site', 'notice');
        if ($text) {
            $action->elementStart('dl', array('id' => 'site_notice',
                                            'class' => 'system_notice'));
            // TRANS: DT element for site notice. String is hidden in default CSS.
            $action->element('dt', null, _($this->topicText));
            $action->elementStart('dd', null);
            $action->raw($text);
            $action->elementEnd('dd');
			$action->element('a', array(
				'id' =>'totw_redirect',
				'href' => $this->redirectLink
			), _($this->redirectText));
            $action->elementEnd('dl');
        }
		return false;
	}

    function onPluginVersion(&$versions)
    {
        $versions[] = array('name' => 'TotWPlugin',
                            'version' => STATUSNET_VERSION,
                            'author' => 'RedEnchilada',
                            'homepage' => 'http://rainbowdash.net/user/798',
                            'rawdescription' =>
                            _m('Changes to site notice render to better suit TotW\'s needs.'));
        return true;
    }
	
	function onEndShowScripts($action) {
		$script = <<<PORK
$('.nav_dropdown span').replaceWith(function() {
	return '<span><a onClick="$(this).closest(\'.nav_dropdown\').toggleClass(\'opened\')">'+$(this).html()+'</a></span>';
});
PORK;
		$action->inlineScript($script);
	}
}