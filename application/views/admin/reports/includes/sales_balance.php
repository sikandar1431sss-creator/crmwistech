<!--  <div id="balance-report" class="hide">
      <?php /*render_datatable(array(
       _l('reports_sales_dt_customers_client'),
       'P-Invoice Total',
       'Invoice Total',
       'P-Inv + Inv Total',
       'Payments Total',
       'Remaining Balance',
       ),'balance-report scroll-responsive'); */?>
   </div>
-->

  <div id="balance-report" class="hide">

      <table class="table table-balance-report scroll-responsive">
          <thead>
          <tr>
              <th><?= _l('reports_sales_dt_customers_client')?> </th>
              <th>P-Invoice Total</th>
              <th>Invoice Total</th>
              <th>P-Inv + Inv Total</th>
              <th>Payments Total</th>
              <th>Adjustments Total</th>
              <th>Bad Debts Total</th>
              <th>Remaining Balance</th>
          </tr>
          </thead>
          <tbody></tbody>
          <tfoot>
          <td></td>
          <td class="pinv"></td>
          <td class="inv"></td>
          <td class="total"></td>
          <td class="paid"></td>
          <td class="adjustment"></td>
          <td class="bad_debts"></td>
          <td class="remaining"></td>
          </tfoot>
      </table>
  </div>

