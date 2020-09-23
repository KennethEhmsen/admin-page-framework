<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods dealing with the options table which use WordPress functions.
 *
 * @since       3.0.1
 * @extends     AdminPageFramework_Utility
 * @package     AdminPageFramework/Utility
 * @internal
 */
class AdminPageFramework_WPUtility_Option extends AdminPageFramework_WPUtility_File {

    /**
     * @param  $sTransientKey
     * @param  mixed $mDefault
     * @return array
     * @since  3.8.23
     */
    static public function getTransientAsArray( $sTransientKey, $mDefault=null ) {
        return self::getAsArray(
            self::getTransient( $sTransientKey, $mDefault )
        );
    }

    /**
     * @param $sTransientKey
     * @param null $mDefault
     * @return array
     * @since  3.8.23
     */
    static public function getTransientWithoutCacheAsArray( $sTransientKey, $mDefault=null ) {
        return self::getAsArray(
            self::getTransientWithoutCache( $sTransientKey, $mDefault )
        );
    }

    /**
     * Retrieve the transient value directly from the database.
     *
     * Similar to the built-in get_transient() method but this one does not use the stored cache in the memory.
     * Used for checking a lock in a sub-routine that should not run simultaneously.
     *
     * @param   string  $sTransientKey
     * @param   mixed   $mDefault
     * @sicne   3.8.23
     * @return  mixed|false `false` on failing to retrieve the transient value.
     */
    static public function getTransientWithoutCache( $sTransientKey, $mDefault=null ) {

        /**
         * @var wpdb $_oWPDB
         */
        $_oWPDB         = $GLOBALS[ 'wpdb' ];
        $_sTableName    = $_oWPDB->options;
        $_sSQLQuery     = "SELECT o1.option_value FROM `{$_sTableName}` o1"
            . " INNER JOIN `{$_sTableName}` o2"
            . " WHERE o1.option_name = %s "
            . " AND o2.option_name = %s "
            . " AND o2.option_value >= UNIX_TIMESTAMP() " // timeout value >= current time
            . " LIMIT 1";
        $_mData = $_oWPDB->get_var(
            $_oWPDB->prepare(
                $_sSQLQuery,
                '_transient_' . $sTransientKey,
                '_transient_timeout_' . $sTransientKey
            )
        );
        return is_null( $_mData )
            ? $mDefault
            : maybe_unserialize( $_mData );

    }

    /**
     * Stores whether the page is loaded in the network admin or not.
     * @since 3.1.3
     */
    static private $_bIsNetworkAdmin;

    /**
     * Deletes the given transient.
     *
     * @since 3.1.3
     * @param   string  $sTransientKey
     * @return  boolean True if the transient was deleted, false otherwise.
     */
    static public function deleteTransient( $sTransientKey ) {

        // Temporarily disable `$_wp_using_ext_object_cache`.
        global $_wp_using_ext_object_cache;
        $_bWpUsingExtObjectCacheTemp    = $_wp_using_ext_object_cache;
        $_wp_using_ext_object_cache     = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin )
            ? self::$_bIsNetworkAdmin
            : is_network_admin();

        $sTransientKey = self::_getCompatibleTransientKey(
            $sTransientKey,
            // @todo it is said as of WordPress 4.3, it will be 255 since the database table column type becomes VARCHAR(255).
            self::$_bIsNetworkAdmin
                ? 40
                : 45
        );

        $_aFunctionNames = array(
            0 => 'delete_transient',
            1 => 'delete_site_transient',
        );
        $_vTransient = $_aFunctionNames[ ( integer ) self::$_bIsNetworkAdmin ]( $sTransientKey );

        // reset prior value of $_wp_using_ext_object_cache
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp;

        return $_vTransient;
    }
    /**
     * Retrieves the given transient.
     *
     * @since   3.1.3
     * @since   3.1.5   Added the $vDefault parameter.
     * @param   string  $sTransientKey
     * @param   mixed   $vDefault
     * @return  mixed
     */
    static public function getTransient( $sTransientKey, $vDefault=null ) {

        // Temporarily disable $_wp_using_ext_object_cache
        global $_wp_using_ext_object_cache;
        $_bWpUsingExtObjectCacheTemp    = $_wp_using_ext_object_cache;
        $_wp_using_ext_object_cache     = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin )
            ? self::$_bIsNetworkAdmin
            : is_network_admin();

        $sTransientKey = self::_getCompatibleTransientKey(
            $sTransientKey,
            // @todo it is said as of WordPress 4.3, it will be 255 since the database table column type becomes VARCHAR(255).
            self::$_bIsNetworkAdmin
                ? 40
                : 45
        );

        $_aFunctionNames = array(
            0 => 'get_transient',
            1 => 'get_site_transient',
        );
        $_vTransient = $_aFunctionNames[ ( integer ) self::$_bIsNetworkAdmin ]( $sTransientKey );

        // Restore the prior value of `$_wp_using_ext_object_cache`.
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp;

        return null === $vDefault
            ? $_vTransient
            : ( false === $_vTransient
                ? $vDefault
                : $_vTransient
            );

    }
    /**
     * Sets the given transient.
     *
     * @since       3.1.3
     * @return      boolean     True if set; otherwise, false.
     * @param       string      $sTransientKey
     * @param       mixed       $vValue
     * @param       integer     $iExpiration
     */
    static public function setTransient( $sTransientKey, $vValue, $iExpiration=0 ) {

        // Temporarily disable `$_wp_using_ext_object_cache`.
        global $_wp_using_ext_object_cache;
        $_bWpUsingExtObjectCacheTemp    = $_wp_using_ext_object_cache;
        $_wp_using_ext_object_cache     = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin )
            ? self::$_bIsNetworkAdmin
            : is_network_admin();

        $sTransientKey = self::_getCompatibleTransientKey(
            $sTransientKey,
            // @todo it is said as of WordPess 4.3, it will be 255 since the database table column type becomes VARCHAR(255).
            self::$_bIsNetworkAdmin
                ? 40
                : 45
        );

        $_aFunctionNames = array(
            0 => 'set_transient',
            1 => 'set_site_transient',
        );
        $_bIsSet = $_aFunctionNames[ ( integer ) self::$_bIsNetworkAdmin ]( $sTransientKey, $vValue, $iExpiration );

        // Restore the prior value of $_wp_using_ext_object_cache
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp;

        return $_bIsSet;
    }
        /**
         * Returns a compatible transient key when it is too long.
         *
         * @since       3.5.9
         * @see         https://codex.wordpress.org/Function_Reference/set_transient
         * @param       string      $sSubject                       The subject string to format.
         * @param       integer     $iAllowedCharacterLength        The allowed character length for the transient key: 40 for network and 45 for regular sites.
         * The method will replace last ending 33 characters if the given string in the first parameter exceeds the limit. So this number must be greater than 33.
         * @return      string
         */
        static public function _getCompatibleTransientKey( $sSubject, $iAllowedCharacterLength=45 ) {

            // Check if the given string exceeds the length limit.
            if ( strlen( $sSubject ) <= $iAllowedCharacterLength ) {
                return $sSubject;
            }

            // Otherwise, a too long option key is given.
            $_iPrefixLengthToKeep = $iAllowedCharacterLength - 33; //  _ + {md5 32 characters}
            $_sPrefixToKeep       = substr(
                $sSubject,
                0, // start position
                $_iPrefixLengthToKeep - 1 // how many characters to extract
            );
            return $_sPrefixToKeep . '_' . md5( $sSubject );

        }

    /**
     * Retrieves the saved option value from the options table with the given option key, field ID, and section ID by giving a function name.
     *
     * @since   3.0.1
     * @since   3.3.0           Added the <var>$aAdditionalOptions</var> parameter.
     * @param   string          $sOptionKey   the option key of the options table.
     * @param   string|array    $asKey        the field id or the array that represents the key structure consisting of the section ID and the field ID.
     * @param   mixed           $vDefault     the default value that will be returned if nothing is stored.
     * @param   array           $aAdditionalOptions     an additional options array to merge with the options array.
     * @return  mixed
     */
    static public function getOption( $sOptionKey, $asKey=null, $vDefault=null, array $aAdditionalOptions=array() ) {
        return self::_getOptionByFunctionName( $sOptionKey, $asKey, $vDefault, $aAdditionalOptions );
    }
    /**
     * Retrieves the saved option value from the site options table with the given option key, field ID, and section ID by giving a function name.
     *
     * @since   3.1.0
     * @since   3.5.3           Added the $aAdditionalOptions parameter.
     * @param   string          $sOptionKey     the option key of the options table.
     * @param   array|string    $asKey          the field id or the array that represents the key structure consisting of the section ID and the field ID.
     * @param   mixed           $vDefault       the default value that will be returned if nothing is stored.
     * @param   array           $aAdditionalOptions     an additional options array to merge with the options array.
     * @remark  Used in the network admin area.
     * @return  mixed
     */
    static public function getSiteOption( $sOptionKey, $asKey=null, $vDefault=null, array $aAdditionalOptions=array() ) {
        return self::_getOptionByFunctionName( $sOptionKey, $asKey, $vDefault, $aAdditionalOptions, 'get_site_option' );
    }

        /**
         * Retrieves the saved option value from the options table
         * with the given option key, field ID, and section ID by giving a function name.
         *
         * @param $sOptionKey
         * @param null $asKey
         * @param null $vDefault
         * @param array $aAdditionalOptions
         * @param string $sFunctionName
         * @return      mixed
         * @since       3.5.3
         */
        static private function _getOptionByFunctionName( $sOptionKey, $asKey=null, $vDefault=null, array $aAdditionalOptions=array(), $sFunctionName='get_option' ) {

            // Entire options
            if ( ! isset( $asKey ) ) {
                $_aOptions = $sFunctionName(
                    $sOptionKey,
                    isset( $vDefault )
                        ? $vDefault
                        : array()
                );;
                return empty( $aAdditionalOptions )
                    ? $_aOptions
                    : self::uniteArrays(
                        $_aOptions,
                        $aAdditionalOptions
                    );
            }

            // Now either the section ID or field ID is given.
            return self::getArrayValueByArrayKeys(

                // subject array
                self::uniteArrays(
                    self::getAsArray(
                        $sFunctionName( $sOptionKey, array() ), // options data
                        true        // preserve empty
                    ),
                    $aAdditionalOptions
                ),

                // dimensional keys
                self::getAsArray(
                    $asKey,
                    true        // preserve empty. e.g. '0' -> array( 0 )
                ),

                // default
                $vDefault

            );

        }

}