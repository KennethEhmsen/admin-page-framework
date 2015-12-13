<?php
/**
 Admin Page Framework v3.7.4b01 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class AdminPageFramework_Form_View___Script_Widget extends AdminPageFramework_Form_View___Script_Base {
    static public function getScript() {
        return <<<JAVASCRIPTS
(function ( $ ) {         

    $( document ).ready( function() {

        $( document ).ajaxComplete( function( event, XMLHttpRequest, ajaxOptions ) {

            // Determine which ajax request this is (we're after \"save-widget\")
            var _aRequest   = {}, _iIndex, _aSplit, _oWidget;
            var _aPairs     = 'string' === typeof ajaxOptions.data
                ? ajaxOptions.data.split( '&' )
                : {};
            for( _iIndex in _aPairs ) {
                _aSplit = _aPairs[ _iIndex ].split( '=' );
                _aRequest[ decodeURIComponent( _aSplit[ 0 ] ) ] = decodeURIComponent( _aSplit[ 1 ] );
            }
            // Only proceed if this was a widget-save request
            if( _aRequest.action && ( 'save-widget' === _aRequest.action ) ) {
                
                // Locate the widget block
                _oWidget = $( 'input.widget-id[value=\"' + _aRequest['widget-id'] + '\"]' ).parents( '.widget' );

                // Check if it is the framework widget.
                if ( $( _oWidget ).find( '.admin-page-framework-sectionset' ).length <= 0 ) { 
                    return; 
                }   
                
                // Trigger manual save, if this was the save request and if we didn't get the form html response (the wp bug)
                if( ! XMLHttpRequest.responseText )  {                         
                    wpWidgets.save( _oWidget, 0, 1, 0 );
                    return;                            
                }
      
                // We got an response, this could be either our request above, or a correct widget-save call, so fire an event on which we can hook our js.
                $( document ).trigger( 
                    'admin_page_framework_saved_widget', 
                    _oWidget 
                );

            }
        });           
   
    }); 

}( jQuery ));
JAVASCRIPTS;
        
    }
}