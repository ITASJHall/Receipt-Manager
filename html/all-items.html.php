<div class="colmask" style="margin-left: 5%; margin-right: 5%; text-align: center;">
    <a href="/all-items?sort=today">Todays Entries</a>&nbsp;&nbsp;<a href="/all-items?sort=all">All Entries</a>
    <table border="0" style="margin: 0 auto;">
        <tr class="tbl_header">
            <th>Item Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Type</th>
            <th>Size</th>
            <th>Number Purchased</th>
            <th>Savings</th>
            <th>Brand</th>
            <th>Date Purchased</th>
            <?php echo(!$today) ? "<th>Date Entered</th>" : ""; ?>
        </tr>
        <?php

        $stripe = false;
        foreach ($entrys as $entry) {
            $date1 = date_create($entry['time_stamp_purchased']);
            $date2 = date_create($entry['time_stamp_created']);
            // Shade every 2nd line
            $stripe = !$stripe;
            if ($stripe) {
                echo '<tr class="odd"> ';
            } else {
                echo '<tr class="even"> ';
            }

            echo '<td>' . $entry['name'] . '</td>';
            echo '<td>$' . $entry['price'] . '</td>';
            echo '<td>' . $entry['category'] . '</td>';
            echo '<td>' . $entry['type'] . '</td>';
            echo '<td>' . $entry['size'] . '</td>';
            echo '<td>' . $entry['amount'] . '</td>';
            echo '<td>$' . (!empty($entry['savings'])? $entry['savings'] : "0") . '</td>';
            echo '<td>' . $entry['brand'] . '</td>';
            echo '<td>' . date_format($date1, "M jS ") . '</td>';
            echo(!$today) ? '<td>' . date_format($date2, "M jS g:i a ") . '</td>' : '';
            echo '<td>';
            echo '<div class="tbl_header">';
            echo "<a href='" . $link_url . "/edit-item?ID=" . $entry['id'] . "'>Edit Item</a> ";
            echo '</div>';
            echo '</td>';
            echo '</tr>';

        }
        ?>
    </table>
</div>