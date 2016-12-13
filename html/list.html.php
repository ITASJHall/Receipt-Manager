<?php if (!empty($entrys)) { ?>

  <?php if(empty($_POST['view'])){ ?>
   <div class="colmask-2">
    <form action="?submit" method="post"  style="text-align: center;">
     <table border="0" style="margin: 0 auto;">
      <tr class="tbl_header">
       <th>Select</th>
       <th>Location Purchased</th>
       <th>Type</th>
       <th>Item Purchesed</th>
       <th>Subtotal</th>
       <th>Tax</th>
       <th>Cost</th>
       <th>Savings</th>
       <th>Points earned</th>
       <th>Dated of Purchased</th>
       <th>Purchaser</th>
       <th>Method Of Payment</th>
      </tr>
      <?php

      $stripe = false;
      foreach ($entrys as $entry) {
       $date = date_create($entry['time_purchased']);
       // Shade every 2nd line
       $stripe = !$stripe;
       if ($stripe) {
        echo '<tr class="odd"> ';
       } else {
        echo '<tr class="even"> ';
       }

       echo '<td><input type="checkbox" name="view[]" value="' . $entry['id'] . '" /></td>';
       echo '<td>' . $entry['location'] . '</td>';
       echo '<td>' . $entry['type'] . '</td>';
       echo '<td>' . $entry['num_items'] . '</td>';
       echo '<td>$' . $entry['cost_before_tax'] . '</td>';
       echo '<td>PST($' . $entry['pst'] . ') - GST($' . $entry['gst'] . ')</td>';
       echo '<td>$' . $entry['cost'] . '</td>';
       echo '<td>$' . $entry['savings_total'] . '</td>';
       echo '<td>' . ($entry['points_earned'] - $entry['points_spent']) . '</td>';
       echo '<td>' . date_format($date, "M jS ") . '</td>';
       echo '<td>' . $entry['purchaser'] . '</td>';
       echo '<td>' . $entry['method_of_payment'] . '</td>';
       echo '</tr>';

      }
      ?>
     </table>
     <button type="submit" name="list">List Receipts</button>
    </form>
  </div>
 <?php } else { ?>
  <div class="colmask-2"></div>
 <?php } ?>
<?php } ?>
