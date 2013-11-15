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
		
		if($this->trimmed('sort') == 'alpha')
			$v->orderBy('yt_name ASC');
		else if($this->trimmed('sort') == 'played')
			$v->orderBy('started DESC');
		
        $v->find();
		
		$vidCount = 0;
		$totalLength = 0;
		
		$this->elementStart('p');
		
		$this->text(_('Sort by:'));
		$this->element('a', array('href' => common_local_url('managevideosync')), _('Time added'));
		$this->text('|');
		$this->element('a', array('href' => common_local_url('managevideosync') . '?sort=played'), _('Time last played'));
		$this->text('|');
		$this->element('a', array('href' => common_local_url('managevideosync') . '?sort=alpha'), _('Alphabetical'));
		
		$this->elementEnd('p');
		
		while($v->fetch()) {
			$vidCount++;
			$totalLength += $v->duration;
			$this->elementStart('div', 'videosync_module');
			$this->elementStart('h2');
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
				'onclick' => "$('#videosync_update-form-" . $v->id . "').toggle()"
			), _('Edit'));
			$form = new VideoDeleteForm($this, $v);
			$form->show();
			$this->elementEnd('div');
			$form = new VideoUpdateForm($this, $v);
			$form->show();
			$this->elementEnd('div');
		}
		
		$lengthFormatted = intval($totalLength/3600) . ':' . (intval($totalLength/60) % 60 < 10 ? '0' : '')
			. (intval($totalLength/60) % 60) . ':' . ($totalLength%60 < 10 ? '0' : '') . ($totalLength%60);
		$this->element('p', 'form_guide', sprintf(_('%s videos totalling %s'), $vidCount, $lengthFormatted));
		
		$form = new VideoAddForm($this);
		$form->show();
	}
}
