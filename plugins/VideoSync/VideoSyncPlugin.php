<?php

if (!defined('STATUSNET')) {
    // This check helps protect against security problems;
    // your code file can't be executed directly from the web.
    exit(1);
}

include_once(INSTALLDIR . '/plugins/Realtime/RealtimePlugin.php');
include_once(INSTALLDIR . '/plugins/Meteor/MeteorPlugin.php');

/* At first I was going to extend MeteorPlugin (wouldn't work because of the complications with Realtime), but then I realized instantiating it and calling its functions would probably work. */
class VideoSyncPlugin extends Plugin
{
    public $webserver     = null;
    public $webport       = null;
    public $controlport   = null;
    public $controlserver = null;
    public $channelbase   = null;
    public $persistent    = true;
    public $tag = 'livestream';

    function __construct($webserver=null, $webport=4670, $controlport=4671, $controlserver=null, $channelbase='')
    {  
        global $config;

        $this->webserver     = (empty($webserver)) ? $config['site']['server'] : $webserver;
        $this->webport       = $webport;
        $this->controlport   = $controlport;
        $this->controlserver = (empty($controlserver)) ? $webserver : $controlserver;
        $this->channelbase   = $channelbase;

        parent::__construct();
    }

    function onRouterInitialized($m) {
        $m->connect('main/switchvideo',
            array('action' => 'switchvideo')
        );
		$m->connect('main/updatestream',
			array('action' => 'updatestream')
		);
		$m->connect('main/videosync',
			array('action' => 'managevideosync')
		);
		$m->connect('main/videosync/update',
			array('action' => 'updatevideo')
		);
		$m->connect('main/videosync/add',
			array('action' => 'addvideo')
		);
		$m->connect('main/videosync/delete',
			array('action' => 'removevideo')
		);
		$m->connect('main/videosync/promote/:id',
			array('action' => 'makevideosyncadmin',
			'id' => '[0-9]+')
		);

        return true;
    }

    function onAutoload($cls) {
        $dir = dirname(__FILE__);

        switch ($cls) {
        case 'SwitchvideoAction':
        case 'AddvideoAction':
        case 'RemovevideoAction':
        case 'UpdatevideoAction':
        case 'ManagevideosyncAction':
            require_once $dir . '/' . strtolower(mb_substr($cls, 0, -6)) . '.php';
            return false;
        case 'Videosync':
        case 'VideosyncAdmin':
            require_once $dir . '/' . $cls . '.php';
            return false;
		case 'UpdatestreamAction':
			
            $m = $this->getMeteor();

            $m->_connect();
            $m->_publish($this->channelbase . '-videosync', array('yt_id' => $this->v->yt_id, 'pos' => time() - $this->v->started, 'started' => strtotime($this->v->started), 'tag' => $this->getFullTag()));
            $m->_disconnect();
			
			exit(0);
			return false;
			
        case 'SwitchForm':
        case 'VideoUpdateForm':
        case 'VideoSetPlayingForm':
        case 'VideoDeleteForm':
        case 'VideoAddForm':
            require_once $dir . '/' . strtolower($cls) . '.php';
			return false;
        default:
            return true;
        }
    }

    function initialize() {
        $this->v = Videosync::getCurrent();

    }

    function onCheckSchema() {
        $schema = Schema::get();

        $schema->ensureTable('videosync',
            array(new ColumnDef('id', 'integer', null,
            true, 'PRI', null, null, true),
            new ColumnDef('yt_id', 'varchar', 11, true),
            new ColumnDef('duration', 'integer', 4, true),
            new ColumnDef('tag', 'varchar', 50, true),
            new ColumnDef('yt_name', 'varchar', 255, true),
            new ColumnDef('started', 'int',  null, false),
            new ColumnDef('temporary', 'integer', 1, true, null, false),
        ));
		
		$schema->ensureTable('videosyncadmin',
			array(new ColumnDef('id', 'integer', null, false, 'PRI'))
		);

        return true;
    }

    function getMeteor() {
        return new MeteorPlugin(
            $this->webserver,
            $this->webport,
            $this->controlport,
            $this->controlserver,
            $this->channelbase
        );
    }

    function getFullTag() {
        return $this->tag . ((!empty($this->v->tag)) ? ' #' . $this->v->tag : '');
    }

    function onEndShowScripts($action) {
        if($action instanceof PublicAction
			|| $action instanceof ManagevideosyncAction) {
            //$action->script($this->path('videosync.min.js'));
            $action->script($this->path('videosync.js'));
            $action->inlineScript('Videosync.init(' . json_encode(array(
                'yt_id' => $this->v->yt_id, 
                'started' => $this->v->started,
                'tag' => $this->getFullTag(),
                'channel' => $this->channelbase . '-videosync',
            )) . ');');
        }

        return true;
    }
	
	function onStartTagShowContent($action) {
		$tag = $action->tag;
		$v = Videosync::staticGet('tag', $tag);
		if($v) {
			$action->elementStart('div', 'videosync_tag_info');
			
			$action->element('img', array(
				'src' => '//img.youtube.com/vi/'.$v->yt_id.'/mqdefault.jpg',
				'width' => '160',
				'height' => '90'
			), null);
			
			$action->elementEnd('div');
		}
		return true;
	}

    //function onEndShowHeader($action) {
    function onStartShowSiteNotice($action) {
        $user = common_current_user();

        if(($action instanceof PublicAction
			|| $action instanceof ManagevideosyncAction) && $user) {
            $action->elementStart('div', array('id' => 'videosync'));
            $action->element('input', array(
                'type' => 'button', 
                'id' => 'videosync_btn', 
                'value' => "Watch videos on the #{$this->tag}!")
            );
            if(!empty($user) && VideosyncAdmin::isAdmin($user)) {
                $action->elementStart('div', array('id' => 'videosync_aside'));
                $v = new Videosync();
                $v->find();
                $s = new SwitchForm($action, $v);
                $s->show();
                $action->elementEnd('div');
            }
            $action->element('div', array('id' => 'videosync_box'));
            $action->elementEnd('div');
        }

        return true;
    }
}
