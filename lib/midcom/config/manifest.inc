'name' => 'midcom',
'icon' => 'cogs',
'purecode' => true,
'privileges' => [
    // Midgard core level privileges
    'midgard:update' => [MIDCOM_PRIVILEGE_DENY, MIDCOM_PRIVILEGE_ALLOW],
    'midgard:delete' => [MIDCOM_PRIVILEGE_DENY, MIDCOM_PRIVILEGE_ALLOW],
    'midgard:create' => [MIDCOM_PRIVILEGE_DENY, MIDCOM_PRIVILEGE_ALLOW],
    'midgard:read' => [MIDCOM_PRIVILEGE_ALLOW, MIDCOM_PRIVILEGE_ALLOW],
    'midgard:parameters' => [MIDCOM_PRIVILEGE_ALLOW, MIDCOM_PRIVILEGE_ALLOW],
    'midgard:attachments' => [MIDCOM_PRIVILEGE_ALLOW, MIDCOM_PRIVILEGE_ALLOW],
    'midgard:autoserve_attachment' => MIDCOM_PRIVILEGE_ALLOW,
    'midgard:privileges' => [MIDCOM_PRIVILEGE_DENY, MIDCOM_PRIVILEGE_ALLOW],
    'midgard:owner' => MIDCOM_PRIVILEGE_DENY,

    // MidCOM core level privileges
    'midcom:approve' => MIDCOM_PRIVILEGE_DENY,
    'midcom:component_config' => [MIDCOM_PRIVILEGE_DENY, MIDCOM_PRIVILEGE_ALLOW],
    'midcom:urlname' => MIDCOM_PRIVILEGE_DENY,
    'midcom:isonline' => [MIDCOM_PRIVILEGE_DENY, MIDCOM_PRIVILEGE_ALLOW],
    'midcom:ajax' => MIDCOM_PRIVILEGE_DENY,
    'midcom:centralized_toolbar' => MIDCOM_PRIVILEGE_DENY,
    'midcom:unlock' => MIDCOM_PRIVILEGE_DENY,
],
'class_mapping' => [
    'midgard_article' => 'midcom_db_article',
    'midgard_attachment' => 'midcom_db_attachment',
    'midgard_element' => 'midcom_db_element',
    'midgard_group' => 'midcom_db_group',
    'midgard_member' => 'midcom_db_member',
    'midgard_parameter' => 'midcom_db_parameter',
    'midgard_person' => 'midcom_db_person',
    'midgard_snippet' => 'midcom_db_snippet',
    'midgard_snippetdir' => 'midcom_db_snippetdir',
    'midgard_style' => 'midcom_db_style',
    'midgard_topic' => 'midcom_db_topic',
],
'description' => 'MidCOM framework',
'customdata' => [
    'midcom.services.cron' => [
        [
            'handler' => 'midcom_cron_loginservice',
            'recurrence' => MIDCOM_CRON_HOUR,
        ],
        [
            'handler' => 'midcom_cron_purgedeleted',
            'recurrence' => MIDCOM_CRON_DAY,
        ],
    ],
],