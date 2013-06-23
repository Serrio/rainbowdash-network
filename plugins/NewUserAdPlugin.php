<?php

if (!defined('STATUSNET')) {
    // This check helps protect against security problems;
    // your code file can't be executed directly from the web.
    exit(1);
}

class NewUserAdPlugin extends Plugin {
	function onEndShowHeader($action) {
		if(!common_logged_in() && $action instanceof PublicAction) {
			// Show a little info pane to not-logged-in users on Public to convince them to join
			$action->elementStart('div', array('id' => 'please_join_our_site'));
			$action->raw(<<<PORK
<p>Welcome to <strong>Rainbow Dash Network</strong>, a social networking site for bronies! Whether you want a place to share your creations or just a spot to make some new friends, RDN is the place to be!</p>
<ul>
<li id="get_to_know_us">Get to know your fellow ponies! Share some art you created, talk about episodes, or just chat it up. We even have a synchronized room to watch episodes together!</li>
<li id="join_some_groups">Join groups for people with similar interests, or create your own and start a whole new community! Meetup groups, game clans, and more are all welcome.</li>
<li id="so_many_posts">Post polls, events, and more! We support a variety of posts to suit your every need.</li>
</ul>
<p>Feel free to browse the site to learn more about our community. If you like what you see, <a href="/main/register">register an account and start posting!</a> We're always welcoming to new faces!</p>
PORK
);
			$action->elementEnd('div');
		}
	}

    function onPluginVersion(&$versions)
    {
        $versions[] = array('name' => 'New User Ad',
                            'version' => STATUSNET_VERSION,
                            'author' => 'RedEnchilada',
                            'homepage' => 'http://rainbowdash.net/user/798',
                            'rawdescription' =>
                            // TRANS: Plugin description.
                            _m('Shows an info pane for visitors that might convince them to join the site.'));
        return true;
    }
}