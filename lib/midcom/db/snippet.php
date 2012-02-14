<?php
/**
 * @package midcom.db
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * MidCOM level replacement for the Midgard Snippet record with framework support.
 *
 * The uplink is the owning snippetdir.
 *
 * Note, as with all MidCOM DB layer objects, you should not use the get_by*
 * operations directly, instead, you have to use the constructor's $id parameter.
 *
 * Also, all QueryBuilder operations need to be done by the factory class
 * obtainable through the statically callable new_query_builder() DBA methods.
 *
 * @package midcom.db
 * @see midcom_services_dbclassloader
 */
class midcom_db_snippet extends midcom_core_dbaobject
{
    public $__midcom_class_name__ = __CLASS__;
    public $__mgdschema_class_name__ = 'midgard_snippet';

    static function new_query_builder()
    {
        return midcom::get('dbfactory')->new_query_builder(__CLASS__);
    }

    static function new_collector($domain, $value)
    {
        return midcom::get('dbfactory')->new_collector(__CLASS__, $domain, $value);
    }

    static function &get_cached($src)
    {
        return midcom::get('dbfactory')->get_cached(__CLASS__, $src);
    }

    public function __set($property, $value)
    {
        if (   $property == 'up'
            && extension_loaded('midgard2'))
        {
            $property = 'snippetdir';
        }
        return parent::__set($property, $value);
    }

    public function __get($property)
    {
        if (   $property == 'up'
            && extension_loaded('midgard2'))
        {
            $property = 'snippetdir';
        }
        return parent::__get($property);
    }

    /**
     * Returns the Parent of the Snippet.
     *
     * @return MidgardObject Parent object or null if there is none.
     */
    function get_parent_guid_uncached()
    {
        if ($this->up == 0)
        {
            return null;
        }

        try
        {
            $parent = new midcom_db_snippetdir($this->up);
        }
        catch (midcom_error $e)
        {
            debug_add("Could not load Snippetdir ID {$this->up} from the database, aborting.",
                MIDCOM_LOG_INFO);
            return null;
        }

        return $parent->guid;
    }

    public function get_icon()
    {
        return 'script.png';
    }
}
?>
