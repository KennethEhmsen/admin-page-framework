<?php 
/**
	Admin Page Framework v3.8.0b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2016, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_FieldType_radio extends AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array('radio');
    protected $aDefaultKeys = array('label' => array(), 'attributes' => array(),);
    protected function getStyles() {
        return ".admin-page-framework-field input[type='radio'] {margin-right: 0.5em;} .admin-page-framework-field-radio .admin-page-framework-input-label-container {padding-right: 1em;} .admin-page-framework-field-radio .admin-page-framework-input-container {display: inline;} .admin-page-framework-field-radio .admin-page-framework-input-label-string{display: inline; }";
    }
    protected function getScripts() {
        return '';
    }
    protected function getField($aField) {
        $_aOutput = array();
        foreach ($this->getAsArray($aField['label']) as $_sKey => $_sLabel) {
            $_aOutput[] = $this->_getEachRadioButtonOutput($aField, $_sKey, $_sLabel);
        }
        $_aOutput[] = $this->_getUpdateCheckedScript($aField['input_id']);
        return implode(PHP_EOL, $_aOutput);
    }
    private function _getEachRadioButtonOutput(array $aField, $sKey, $sLabel) {
        $_aAttributes = $aField['attributes'] + $this->getElementAsArray($aField, array('attributes', $sKey));
        $_oRadio = new AdminPageFramework_Input_radio($_aAttributes);
        $_oRadio->setAttributesByKey($sKey);
        $_oRadio->setAttribute('data-default', $aField['default']);
        return $this->getElementByLabel($aField['before_label'], $sKey, $aField['label']) . "<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width: " . $this->sanitizeLength($aField['label_min_width']) . ";'>" . "<label " . $this->getAttributes(array('for' => $_oRadio->getAttribute('id'), 'class' => $_oRadio->getAttribute('disabled') ? 'disabled' : null,)) . ">" . $this->getElementByLabel($aField['before_input'], $sKey, $aField['label']) . $_oRadio->get($sLabel) . $this->getElementByLabel($aField['after_input'], $sKey, $aField['label']) . "</label>" . "</div>" . $this->getElementByLabel($aField['after_label'], $sKey, $aField['label']);
    }
    private function _getUpdateCheckedScript($sInputID) {
        $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    jQuery( 'input[type=radio][data-id=\"{$sInputID}\"]' ).change( function() {
        // Uncheck the other radio buttons
        jQuery( this ).closest( '.admin-page-framework-field' ).find( 'input[type=radio][data-id=\"{$sInputID}\"]' ).attr( 'checked', false );

        // Make sure the clicked item is checked
        jQuery( this ).attr( 'checked', 'checked' );
    });
});                 
JAVASCRIPTS;
        return "<script type='text/javascript' class='radio-button-checked-attribute-updater'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>";
    }
}
