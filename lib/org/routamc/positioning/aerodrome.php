<?php
/**
 * @package org.routamc.positioning
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * MidCOM wrapper class for aerodrome objects
 *
 * <code>
 * $airport = new org_routamc_positioning_aerodrome_dba();
 * $airport->icao = 'FYMG';
 * $airport->iata = 'MQG';
 * $airport->name = 'Midgard Airport';
 * $airport->latitude = -22.083332;
 * $airport->longitude = 17.366667;
 * $airport->create();
 * </code>
 *
 * That's actually a real airport, located in Namibia.
 *
 * @package org.routamc.positioning
 */
class org_routamc_positioning_aerodrome_dba extends midcom_core_dbaobject
{
    public $__midcom_class_name__ = __CLASS__;
    public $__mgdschema_class_name__ = 'org_routamc_positioning_aerodrome';

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

    /**
     * @return org_routamc_positioning_city_dba City the airport caters for
     */
    function get_parent_guid_uncached()
    {
        if ($this->city)
        {
            try
            {
                $parent = new org_routamc_positioning_city_dba($this->city);
            }
            catch (midcom_error $e)
            {
                debug_add("Could not load City ID {$this->city} from the database, aborting.",
                    MIDCOM_LOG_INFO);
                return null;
            }
            return $parent->guid;
        }

        return null;
    }

    /**
     * Human-readable label for cases like Asgard navigation
     */
    function get_label()
    {
        if (!empty($this->name))
        {
            return "{$this->icao} ({$this->name})";
        }
        return $this->icao;
    }

    /**
     * Don't save aerodrome if another aerodrome is in same place or exists with same ICAO
     */
    public function _on_creating()
    {
        if (   $this->longitude
            && $this->latitude)
        {
            $qb = org_routamc_positioning_aerodrome_dba::new_query_builder();
            $qb->add_constraint('longitude', '=', $this->longitude);
            $qb->add_constraint('latitude', '=', $this->latitude);
            $qb->set_limit(1);
            $matches = $qb->execute_unchecked();
            if (count($matches) > 0)
            {
                // We don't need to save duplicate entries
                return false;
            }
        }

        if (!empty($this->icao))
        {
            $qb = org_routamc_positioning_aerodrome_dba::new_query_builder();
            $qb->add_constraint('icao', '=', $this->icao);
            $qb->set_limit(1);
            $matches = $qb->execute_unchecked();
            if (count($matches) > 0)
            {
                // We don't need to save duplicate entries
                midcom_connection::set_error(MGD_ERR_DUPLICATE);
                return false;
            }
        }
        return true;
    }
}
?>