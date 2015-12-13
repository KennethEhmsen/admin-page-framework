<?php
/**
 Admin Page Framework v3.7.4b01 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
abstract class AdminPageFramework_TaxonomyField_Router extends AdminPageFramework_Factory {
    public function __construct($oProp) {
        parent::__construct($oProp);
        if ($this->oProp->bIsAdmin) {
            $this->oUtil->registerAction('wp_loaded', array($this, '_replyToDetermineToLoad'));
        }
    }
    public function _isInThePage() {
        if ('admin-ajax.php' == $this->oProp->sPageNow) {
            return true;
        }
        if ('edit-tags.php' != $this->oProp->sPageNow) {
            return false;
        }
        if (isset($_GET['taxonomy']) && !in_array($_GET['taxonomy'], $this->oProp->aTaxonomySlugs)) {
            return false;
        }
        return true;
    }
    public function _replyToDetermineToLoad() {
        if (!$this->_isInThePage()) {
            return;
        }
        $this->_setUp();
        $this->oUtil->addAndDoAction($this, "set_up_{$this->oProp->sClassName}", $this);
        foreach ($this->oProp->aTaxonomySlugs as $_sTaxonomySlug) {
            add_action("created_{$_sTaxonomySlug}", array($this, '_replyToValidateOptions'), 10, 2);
            add_action("edited_{$_sTaxonomySlug}", array($this, '_replyToValidateOptions'), 10, 2);
            add_action("{$_sTaxonomySlug}_add_form_fields", array($this, '_replyToPrintFieldsWOTableRows'));
            add_action("{$_sTaxonomySlug}_edit_form_fields", array($this, '_replyToPrintFieldsWithTableRows'));
            add_filter("manage_edit-{$_sTaxonomySlug}_columns", array($this, '_replyToManageColumns'), 10, 1);
            add_filter("manage_edit-{$_sTaxonomySlug}_sortable_columns", array($this, '_replyToSetSortableColumns'));
            add_action("manage_{$_sTaxonomySlug}_custom_column", array($this, '_replyToPrintColumnCell'), 10, 3);
        }
    }
}