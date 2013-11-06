<?php

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

class ManagevideosyncAction extends Action
{
    /**
     * Class handler.
     *
     * @param array $args query arguments
     *
     * @return void
     */
    function handle($args)
    {
        parent::handle($args);
        if (!common_logged_in()) {
            $this->clientError(_('Not logged in.'));
            return;
        }
        $user = common_current_user();
        if(!VideosyncAdmin::isAdmin($user)) {
            $this->clientError(_('Not authorized'));
            return;
        }
		$this->showPage();
    }
	
    function title()
    {
        return _('Videosync settings');
    }
	
    function getInstructions()
    {
        return _('Manage the videosync list here.');
    }
	
	function showContent() {
		$current = Videosync::getCurrent();
		$current = $current->id;
		
        $v = new Videosync();
        $v->find();
		
		while($v->fetch()) {
			$this->elementStart('div', 'videosync_module');
			$this->elementStart('h2');
			$this->element('span', 'videosync_identifier', $v->id);
			$this->element('a', array(
				'class' => 'videosync_videoname',
				'href' => '//youtu.be/' . $v->yt_id,
				'rel' => 'external nofollow',
				'target' => '_blank'
			), $v->yt_name);
			if($v->id == $current)
				$this->element('span', 'videosync_nowplaying', _('Now Playing'));
			$this->elementEnd('h2');
			$this->elementStart('div', 'videosync_vidoptions');
			$form = new VideoSetPlayingForm($this, $v);
			$form->show();
			$this->element('button', array(
				'title' => _('Update video information'),
				'onclick' => "$('#videosync_update-form-" . $v->id . "').show()"
			), _('Edit'));
			$form = new VideoDeleteForm($this, $v);
			$form->show();
			$this->elementEnd('div');
			$form = new VideoUpdateForm($this, $v);
			$form->show();
			$this->elementEnd('div');
		}
		
		//$form = new VideoAddForm($this);
		//$form->show();
	}
}
