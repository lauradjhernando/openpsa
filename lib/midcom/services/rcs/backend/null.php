<?php
/**
 * @author tarjei huse
 * @package midcom.services.rcs
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * @package midcom.services.rcs
 */
class midcom_services_rcs_backend_null extends midcom_services_rcs_backend
{
    public function update($updatemessage = null) : bool
    {
        return true;
    }

    public function get_revision($revision) : array
    {
        return [];
    }

    protected function load_history() : array
    {
        return [];
    }
}
