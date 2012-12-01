<?php
/**
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2011, StatusNet, Inc.
 *
 * Primary nav, show on all pages
 *
 * PHP version 5
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Menu
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2011 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET')) {
    // This check helps protect against security problems;
    // your code file can't be executed directly from the web.
    exit(1);
}

/**
 * Primary, top-level menu
 *
 * @category  General
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2011 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

class PrimaryNav extends Menu
{
    function show()
    {
        $user = common_current_user();
        $tmpisadmin = 0;
        if ($user) {
           if ($user->hasRight(Right::CONFIGURESITE)) {
              $tmpisadmin = 1;
           }
        }
        if ($tmpisadmin == 1) {
           $this->elementStart('dl', array('id' => 'site_nav_global_primary', 'style' => 'font-size: 11px;'));
        } else {
           $this->elementStart('dl', array('id' => 'site_nav_global_primary' ));
        }

        // TRANS: DT element for primary navigation menu. String is hidden in default CSS.
        $this->element('dt', null, _('Primary site navigation'));
        $this->elementStart('dd');
        $this->elementStart('ul', array('class' => 'nav'));
        if (Event::handle('StartPrimaryNav', array($this->action))) {

                // TRANS: Tooltip for main menu option "Home".
                $tooltip = _m('TOOLTIP', 'Home');
                $this->menuItem(common_local_url('public'),
                    _m('MENU', 'Home'), $tooltip, false, 'nav_home');

                // TRANS: Tooltip for main menu option "Roleplay".
                $tooltip = _m('TOOLTIP', 'Act out characters in the MLP universe!');
                $this->menuItem('http://rp.rainbowdash.net/',
                                // TRANS: Main menu option when logged in for access to personal profile and friends timeline.
                    _m('MENU', 'Roleplay'), $tooltip, false, 'nav_roleplay');

                // TRANS: Tooltip for main menu option "Meetups".
                $tooltip = _m('TOOLTIP', 'Pony/brony meetups and social groups!');
                $this->menuItem('http://www.bronies.com/map/',
                                // TRANS: Main menu option when logged in for access to personal profile and friends timeline.
                                _m('MENU', 'Meetups'), $tooltip, false, 'nav_meetups');

                // TRANS: Tooltip for main menu option "Rules".
                $tooltip = _m('TOOLTIP', 'Site Rules!');
                $this->menuItem('/doc/rules',
                                // TRANS: Main menu option when logged in for access to personal profile and friends timeline.
                                _m('MENU', 'Rules'), $tooltip, false, 'nav_rules');

                // TRANS: Tooltip for main menu option "Rules".
                $tooltip = _m('TOOLTIP', 'List of Site Staff');
                $this->menuItem('/main/staff',
                                // TRANS: Main menu option when logged in for access to personal profile and friends timeline.
                                _m('MENU', 'Staff'), $tooltip, false, 'nav_admins');



            if ($user) {

                // TRANS: Tooltip for main menu option "Personal".
                $tooltip = _m('TOOLTIP', 'Personal profile and friends timeline');
                $this->menuItem(common_local_url('all', array('nickname' => $user->nickname)),
                                // TRANS: Main menu option when logged in for access to personal profile and friends timeline.
                                _m('MENU', 'Personal'), $tooltip, false, 'nav_personal');


                // TRANS: Tooltip for main menu option "Account".
                $tooltip = _m('TOOLTIP', 'Change your email, avatar, password, profile');
                $this->menuItem(common_local_url('profilesettings'),
                                // TRANS: Main menu option when logged in for access to user settings.
                                _('Account'), $tooltip, false, 'nav_account');
                // TRANS: Tooltip for main menu option "Services".
               $tooltip = _m('TOOLTIP', 'Connect to services');
               $this->menuItem(common_local_url('oauthconnectionssettings'),
                                // TRANS: Main menu option when logged in and connection are possible for access to options to connect to other services.
                                _('Connect'), $tooltip, false, 'nav_connect');
                if ($user->hasRight(Right::CONFIGURESITE)) {
                    // TRANS: Tooltip for menu option "Admin".
                    $tooltip = _m('TOOLTIP', 'Change site configuration');
                    $this->menuItem(common_local_url('siteadminpanel'),
                                    // TRANS: Main menu option when logged in and site admin for access to site configuration.
                                    _m('MENU', 'Admin'), $tooltip, false, 'nav_admin');
                }
#                if (common_config('invite', 'enabled')) {
#                    // TRANS: Tooltip for main menu option "Invite".
#                    $tooltip = _m('TOOLTIP', 'Invite friends and colleagues to join you on %s');
#                    $this->menuItem(common_local_url('invite'),
#                                    // TRANS: Main menu option when logged in and invitations are allowed for inviting new users.
#                                    _m('MENU', 'Invite'),
#                                    sprintf($tooltip,
#                                            common_config('site', 'name')),
#                                    false, 'nav_invitecontact');
#                }
                // TRANS: Tooltip for main menu option "Logout"
                $tooltip = _m('TOOLTIP', 'Logout from the site');
                $this->menuItem(common_local_url('logout'),
                                // TRANS: Main menu option when logged in to log out the current user.
                                _m('MENU', 'Logout'), $tooltip, false, 'nav_logout');
            }
            else {
                if (!common_config('site', 'closed') && !common_config('site', 'inviteonly')) {
                    // TRANS: Tooltip for main menu option "Register".
                    $tooltip = _m('TOOLTIP', 'Create an account');
                    $this->menuItem(common_local_url('register'),
                                    // TRANS: Main menu option when not logged in to register a new account.
                                    _m('MENU', 'Register'), $tooltip, false, 'nav_register');
                }
                // TRANS: Tooltip for main menu option "Login".
                $tooltip = _m('TOOLTIP', 'Login to the site');
                $this->menuItem(common_local_url('login'),
                                // TRANS: Main menu option when not logged in to log in.
                                _m('MENU', 'Login'), $tooltip, false, 'nav_login');
            }
#            // TRANS: Tooltip for main menu option "Help".
#            $tooltip = _m('TOOLTIP', 'Help me!');
#            $this->menuItem(common_local_url('doc', array('title' => 'help')),
#                            // TRANS: Main menu option for help on the StatusNet site.
#                            _m('MENU', 'Help'), $tooltip, false, 'nav_help');
            if ($user || !common_config('site', 'private')) {
                // TRANS: Tooltip for main menu option "Search".
                $tooltip = _m('TOOLTIP', 'Search for people or text');
                $this->menuItem(common_local_url('peoplesearch'),
                                // TRANS: Main menu option when logged in or when the StatusNet instance is not private.
                                _m('MENU', 'Search'), $tooltip, false, 'nav_search');
            }
            Event::handle('EndPrimaryNav', array($this->action));
        }
        $this->elementEnd('ul');
        $this->elementEnd('dd');
        $this->elementEnd('dl');
    }
}
