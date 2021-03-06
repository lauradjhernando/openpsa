<?php
/**
 * @package org.openpsa.sales
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

/**
 * Interface for deliverable invoicing
 *
 * @package org.openpsa.sales
 */
interface org_openpsa_invoices_interfaces_calculator
{
    /**
     * Perform the cost/price calculation
     */
    public function run(org_openpsa_sales_salesproject_deliverable_dba $deliverable);

    /**
     * Returns the calculated cost
     */
    public function get_cost() : float;

    /**
     * Returns the calculated price
     */
    public function get_price() : float;

    /**
     * Returns the invoice items that should be written
     *
     * @return org_openpsa_invoices_invoice_item_dba[]
     */
    public function get_invoice_items(org_openpsa_invoices_invoice_dba $invoice) : array;

    /**
     * Returns identifier number for next invoice
     */
    public function generate_invoice_number() : int;
}
