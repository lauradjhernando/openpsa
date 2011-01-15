<?php
/**
 * @package net.nehmer.comments
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Comments site interface class
 *
 * See the various handler classes for details.
 *
 * @package net.nehmer.comments
 */
class net_nehmer_comments_viewer extends midcom_baseclasses_components_request
{
    /**
     * Populates the node toolbar depending on the user's rights.
     *
     * @access protected
     */
    function _populate_node_toolbar()
    {
        if (   $this->_topic->can_do('midgard:update')
            && $this->_topic->can_do('midcom:component_config'))
        {
            $this->_node_toolbar->add_item
            (
                array
                (
                    MIDCOM_TOOLBAR_URL => 'config/',
                    MIDCOM_TOOLBAR_LABEL => $this->_l10n_midcom->get('component configuration'),
                    MIDCOM_TOOLBAR_HELPTEXT => $this->_l10n_midcom->get('component configuration helptext'),
                    MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/stock_folder-properties.png',
                )
            );
        }
        if (   $this->_topic->can_do('midgard:update')
            && $this->_topic->can_do('net.nehmer.comments:moderation'))
        {
            $this->_node_toolbar->add_item
            (
                array
                (
                    MIDCOM_TOOLBAR_URL => 'moderate/reported_abuse/',
                    MIDCOM_TOOLBAR_LABEL => $this->_l10n_midcom->get('reported abuse'),
                    MIDCOM_TOOLBAR_HELPTEXT => $this->_l10n_midcom->get('reported abuse helptext'),
                    MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/stock_news.png',
                )
            );
            $this->_node_toolbar->add_item
            (
                array
                (
                    MIDCOM_TOOLBAR_URL => 'moderate/abuse/',
                    MIDCOM_TOOLBAR_LABEL => $this->_l10n_midcom->get('abuse'),
                    MIDCOM_TOOLBAR_HELPTEXT => $this->_l10n_midcom->get('abuse helptext'),
                    MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/stock_news.png',
                )
            );
            $this->_node_toolbar->add_item
            (
                array
                (
                    MIDCOM_TOOLBAR_URL => 'moderate/junk/',
                    MIDCOM_TOOLBAR_LABEL => $this->_l10n_midcom->get('junk'),
                    MIDCOM_TOOLBAR_HELPTEXT => $this->_l10n_midcom->get('junk helptext'),
                    MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/stock_news.png',
                )
            );
            $this->_node_toolbar->add_item
            (
                array
                (
                    MIDCOM_TOOLBAR_URL => 'moderate/latest/',
                    MIDCOM_TOOLBAR_LABEL => $this->_l10n_midcom->get('latest comments'),
                    MIDCOM_TOOLBAR_HELPTEXT => $this->_l10n_midcom->get('latest helptext'),
                    MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/stock_news.png',
                )
            );
            $this->_node_toolbar->add_item
            (
                array
                (
                    MIDCOM_TOOLBAR_URL => 'moderate/latest_new/',
                    MIDCOM_TOOLBAR_LABEL => $this->_l10n_midcom->get('only new'),
                    MIDCOM_TOOLBAR_HELPTEXT => $this->_l10n_midcom->get('only new helptext'),
                    MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/stock_news.png',
                )
            );
            $this->_node_toolbar->add_item
            (
                array
                (
                    MIDCOM_TOOLBAR_URL => 'moderate/latest_approved/',
                    MIDCOM_TOOLBAR_LABEL => $this->_l10n_midcom->get('only approved'),
                    MIDCOM_TOOLBAR_HELPTEXT => $this->_l10n_midcom->get('only approved helptext'),
                    MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/stock_news.png',
                )
            );
        }
    }

    public function _populate_post_toolbar($comment)
    {
        $toolbar = new midcom_helper_toolbar();

        if (   $_MIDCOM->auth->user
            && $comment->status < NET_NEHMER_COMMENTS_MODERATED)
        {
            if (!$comment->can_do('net.nehmer.comments:moderation'))
            {
                // Regular users can only report abuse
                $toolbar->add_item
                (
                    array
                    (
                        MIDCOM_TOOLBAR_URL => "report/{$comment->guid}/",
                        MIDCOM_TOOLBAR_LABEL => $this->_l10n->get('report abuse'),
                        MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/stock_help-agent.png',
//                        MIDCOM_TOOLBAR_ENABLED =>  $comment->can_do('midgard:update'),
                        MIDCOM_TOOLBAR_POST => true,
                        MIDCOM_TOOLBAR_POST_HIDDENARGS => array
                        (
                            'mark' => 'abuse',
                            'return_url' => midcom_connection::get_url('uri'),
                        )
                    )
                );
            }
            else
            {
                $toolbar->add_item
                (
                    array
                    (
                        MIDCOM_TOOLBAR_URL => "report/{$comment->guid}/",
                        MIDCOM_TOOLBAR_LABEL => $this->_l10n->get('confirm abuse'),
                        MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/trash.png',
                        MIDCOM_TOOLBAR_ENABLED => $comment->can_do('net.nehmer.comments:moderation'),
                        MIDCOM_TOOLBAR_POST => true,
                        MIDCOM_TOOLBAR_POST_HIDDENARGS => array
                        (
                            'mark' => 'confirm_abuse',
                            'return_url' => midcom_connection::get_url('uri'),
                        )
                    )
                );
                $toolbar->add_item
                (
                    array
                    (
                        MIDCOM_TOOLBAR_URL => "report/{$comment->guid}/",
                        MIDCOM_TOOLBAR_LABEL => $this->_l10n->get('confirm junk'),
                        MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/trash.png',
                        MIDCOM_TOOLBAR_ENABLED => $comment->can_do('net.nehmer.comments:moderation'),
                        MIDCOM_TOOLBAR_POST => true,
                        MIDCOM_TOOLBAR_POST_HIDDENARGS => array
                        (
                            'mark' => 'confirm_junk',
                            'return_url' => midcom_connection::get_url('uri'),
                        )
                    )
                );
                $toolbar->add_item
                (
                    array
                    (
                        MIDCOM_TOOLBAR_URL => "report/{$comment->guid}/",
                        MIDCOM_TOOLBAR_LABEL => $this->_l10n->get('not abuse'),
                        MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/ok.png',
                        MIDCOM_TOOLBAR_ENABLED => $comment->can_do('net.nehmer.comments:moderation'),
                        MIDCOM_TOOLBAR_POST => true,
                        MIDCOM_TOOLBAR_POST_HIDDENARGS => array
                        (
                            'mark' => 'not_abuse',
                            'return_url' => midcom_connection::get_url('uri'),
                        )
                    )
                );
                $toolbar->add_item
                (
                    array
                    (
                        MIDCOM_TOOLBAR_URL => $_SERVER['REQUEST_URI'],
                        MIDCOM_TOOLBAR_LABEL => $this->_l10n->get('delete'),
                        MIDCOM_TOOLBAR_ICON => 'stock-icons/16x16/editdelete.png',
                        MIDCOM_TOOLBAR_ENABLED => $comment->can_do('net.nehmer.comments:moderation'),
                        MIDCOM_TOOLBAR_POST => true,
                        MIDCOM_TOOLBAR_POST_HIDDENARGS => array
                        (
                            'net_nehmer_comment_adminsubmit' => '1',
                            'guid' => $comment->guid,
                            'action_delete' => 'action_delete',
                        )
                    )
                );
            }
        }
        return $toolbar;
    }

    /**
     * Generic request startup work:
     * - Populate the Node Toolbar
     */
    function _on_handle($handler, $args)
    {
        $this->_populate_node_toolbar();

        return true;
    }
}

?>