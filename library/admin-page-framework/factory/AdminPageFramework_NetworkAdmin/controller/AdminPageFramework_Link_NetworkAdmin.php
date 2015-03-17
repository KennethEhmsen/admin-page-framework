<?php
/**
 Admin Page Framework v3.5.6 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class AdminPageFramework_Link_NetworkAdmin extends AdminPageFramework_Link_Page {
    public function __construct($oProp, $oMsg = null) {
        parent::__construct($oProp, $oMsg);
        if (in_array($this->oProp->sPageNow, array('plugins.php')) && 'plugin' === $this->oProp->aScriptInfo['sType']) {
            remove_filter('plugin_action_links_' . plugin_basename($this->oProp->aScriptInfo['sPath']), array($this, '_replyToAddSettingsLinkInPluginListingPage'), 20);
            add_filter('network_admin_plugin_action_links_' . plugin_basename($this->oProp->aScriptInfo['sPath']), array($this, '_replyToAddSettingsLinkInPluginListingPage'));
        }
    }
    protected $_sFilterSuffix_PluginActionLinks = 'network_admin_plugin_action_links_';
}