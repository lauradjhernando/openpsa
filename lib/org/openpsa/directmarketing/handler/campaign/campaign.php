<?php
/**
 * @package org.openpsa.directmarketing
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

use midcom\datamanager\datamanager;
use midcom\datamanager\schemadb;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @package org.openpsa.directmarketing
 */
class org_openpsa_directmarketing_handler_campaign_campaign extends midcom_baseclasses_components_handler
implements org_openpsa_widgets_grid_provider_client
{
    use org_openpsa_directmarketing_handler;

    /**
     * The campaign which has been created
     *
     * @var org_openpsa_directmarketing_campaign_dba
     */
    private $_campaign = null;

    /**
     *
     * @var array
     */
    private $memberships;

    /**
     * @var datamanager
     */
    private $_datamanager;

    public function get_qb($field = null, $direction = 'ASC', array $search = [])
    {
        $query = org_openpsa_contacts_person_dba::new_query_builder();
        $query->get_doctrine()
            ->leftJoin('org_openpsa_campaign_member', 'm', Join::WITH, 'm.person = c.id')
            ->where('m.orgOpenpsaObtype <> :tester AND m.orgOpenpsaObtype <> :unsubscribed AND m.campaign = :campaign')
            ->setParameters([
                'tester' => org_openpsa_directmarketing_campaign_member_dba::TESTER,
                'unsubscribed' => org_openpsa_directmarketing_campaign_member_dba::UNSUBSCRIBED,
                'campaign' => $this->_campaign->id
            ]);

        if (!is_null($field)) {
            $query->add_order($field, $direction);
        }
        // Set the order
        $query->add_order('lastname', 'ASC');
        $query->add_order('firstname', 'ASC');
        $query->add_order('email', 'ASC');

        return $query;
    }

    public function get_row(midcom_core_dbaobject $person)
    {
        $siteconfig = org_openpsa_core_siteconfig::get_instance();
        $url = $siteconfig->get_node_full_url('org.openpsa.contacts') . 'person/';

        $row = [
            'id' => $person->id,
            'index_firstname' => $person->firstname,
            'firstname' => '<a target="_blank" href="' . $url . $person->guid . '/">' . $person->firstname . '</a>',
            'index_lastname' => $person->lastname,
            'lastname' => '<a target="_blank" href="' . $url . $person->guid . '/">' . $person->lastname . '</a>',
            'index_email' => $person->email,
            'email' => '<a target="_blank" href="' . $url . $person->guid . '/">' . $person->email . '</a>'
        ];

        $delete_string = sprintf($this->_l10n->get('remove %s from campaign'), $person->name);
        $row['delete'] = '<i class="fa fa-trash" data-person-guid="' . $person->guid . '" data-member-guid="' . $this->memberships[$person->id]['guid'] . '" title="' . $delete_string . '"></i>';

        $row['bounced'] = '';
        if ($this->memberships[$person->id]['orgOpenpsaObtype'] == org_openpsa_directmarketing_campaign_member_dba::BOUNCED) {
            $row['bounced'] = '<i class="fa fa-exclamation-triangle" title="' . sprintf($this->_l10n->get('%s has bounced'), $person->email) . '"></i>';
        }

        return $row;
    }

    /**
     * Looks up an campaign to display.
     */
    public function _handler_view($handler_id, array $args, array &$data)
    {
        $this->_campaign = $this->load_campaign($args[0]);

        $this->_datamanager = datamanager::from_schemadb($this->_config->get('schemadb_campaign'));
        $this->_datamanager->set_storage($this->_campaign);

        $this->_request_data['campaign'] = $this->_campaign;
        $this->_request_data['datamanager'] = $this->_datamanager;

        org_openpsa_widgets_contact::add_head_elements();
        $this->_populate_toolbar();

        $provider = new org_openpsa_widgets_grid_provider($this);
        $data['grid'] = $provider->get_grid('list_members_' . $this->_campaign->guid);

        // Populate calendar events for the campaign
        $this->bind_view_to_object($this->_campaign, $this->_datamanager->get_schema()->get_name());
        midcom::get()->metadata->set_request_metadata($this->_campaign->metadata->revised, $this->_campaign->guid);
        midcom::get()->head->set_pagetitle($this->_campaign->title);
        $data['view_campaign'] = $this->_datamanager->get_content_html();

        return $this->show('show-campaign');
    }

    private function _populate_toolbar()
    {
        $workflow = $this->get_workflow('datamanager');
        $buttons = [];
        if ($this->_campaign->can_do('midgard:update')) {
            $buttons[] = $workflow->get_button("campaign/edit/{$this->_campaign->guid}/", [
                MIDCOM_TOOLBAR_ACCESSKEY => 'e',
            ]);
        }

        if ($this->_campaign->can_do('midgard:delete')) {
            $delete_workflow = $this->get_workflow('delete', ['object' => $this->_campaign]);
            $buttons[] = $delete_workflow->get_button("campaign/delete/{$this->_campaign->guid}/");
        }

        if ($this->_campaign->orgOpenpsaObtype == org_openpsa_directmarketing_campaign_dba::TYPE_SMART) {
            //Edit query parameters button in case 1) not in edit mode 2) is smart campaign 3) can edit
            $buttons[] = [
                MIDCOM_TOOLBAR_URL => "campaign/edit_query/{$this->_campaign->guid}/",
                MIDCOM_TOOLBAR_LABEL => $this->_l10n->get('edit rules'),
                MIDCOM_TOOLBAR_GLYPHICON => 'filter',
                MIDCOM_TOOLBAR_ENABLED => $this->_campaign->can_do('midgard:update'),
            ];
        } else {
            // Import button if we have permissions to create users
            $buttons[] = [
                MIDCOM_TOOLBAR_URL => "campaign/import/{$this->_campaign->guid}/",
                MIDCOM_TOOLBAR_LABEL => $this->_l10n->get('import subscribers'),
                MIDCOM_TOOLBAR_GLYPHICON => 'upload',
                MIDCOM_TOOLBAR_ENABLED => midcom::get()->auth->can_user_do('midgard:create', null, org_openpsa_contacts_person_dba::class),
            ];
        }
        $buttons[] = [
            MIDCOM_TOOLBAR_URL => "campaign/export/csv/{$this->_campaign->guid}/",
            MIDCOM_TOOLBAR_LABEL => $this->_l10n->get('export as csv'),
            MIDCOM_TOOLBAR_GLYPHICON => 'download',
        ];

        if ($this->_campaign->can_do('midgard:create')) {
            $schemadb = schemadb::from_path($this->_config->get('schemadb_message'));
            foreach ($schemadb->all() as $name => $schema) {
                $buttons[] = $workflow->get_button("message/create/{$this->_campaign->guid}/{$name}/", [
                    MIDCOM_TOOLBAR_LABEL => sprintf($this->_l10n->get('new %s'), $this->_l10n->get($schema->get('description'))),
                    MIDCOM_TOOLBAR_GLYPHICON => $this->get_messagetype_icon($schema->get('customdata')['org_openpsa_directmarketing_messagetype']),
                ]);
            }
        }
        $this->_view_toolbar->add_items($buttons);
    }

    private function get_messagetype_icon($type)
    {
        $icon = 'envelope-o';
        switch ($type) {
            case org_openpsa_directmarketing_campaign_message_dba::SMS:
            case org_openpsa_directmarketing_campaign_message_dba::MMS:
                $icon = 'mobile';
                break;
            case org_openpsa_directmarketing_campaign_message_dba::CALL:
                $icon = 'phone';
                break;
            case org_openpsa_directmarketing_campaign_message_dba::FAX:
                $icon = 'fax';
                break;
            case org_openpsa_directmarketing_campaign_message_dba::SNAILMAIL:
                $icon = 'home';
                break;
        }
        return $icon;
    }

    /**
     * Displays campaign members.
     *
     * @param mixed $handler_id The ID of the handler.
     * @param array $args The argument list.
     * @param array &$data The local request data.
     */
    public function _handler_members($handler_id, array $args, array &$data)
    {
        $this->_campaign = $this->load_campaign($args[0]);

        midcom::get()->skip_page_style = true;

        $data['provider'] = new org_openpsa_widgets_grid_provider($this);
        return $this->show('show-campaign-members');
    }
}
