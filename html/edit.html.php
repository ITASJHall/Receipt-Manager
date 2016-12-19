<?php if(!empty($errors)){ ?>
    <div class="error"><?=implode(', ',$errors); ?></div>
<?php } elseif(!empty($successes)){ ?>
    <div class="success"><?=implode(', ',$successes); ?></div>
<?php } ?>

<div class="colmask-2"  style="margin-left: 5%; margin-right: 5%;">
    <form action="?submit&ID=<?=$entry_id ?>" method="post"  style="text-align: center;">
        <div>
            <table border="0" style="margin: 0 auto;">
                <tr class="tbl_header">
                    <th>Location Purchased</th>
                    <th>Transaction ID</th>
                    <th>Subtotal</th>
                    <th>PST</th>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="location" value="<?=$entrys['location']; ?>" >
                    </td>
                    <td>
                        <input type="text" name="transaction_id" value="<?=$entrys['tr_id']; ?>" >
                    </td>
                    <td>
                        <input type="text" name="cost_before_tax" value="<?=$entrys['cost_before_tax']; ?>" >
                    </td>
                    <td>
                        <input type="text" name="pst" value="<?=$entrys['pst']; ?>" >
                    </td>
                </tr>
                <tr class="tbl_header">
                    <th>GST</th>
                    <th>Cost</th>
                    <th>Savings</th>
                    <th>Points Earned</th>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="gst" value="<?=$entrys['gst']; ?>" >
                    </td>
                    <td>
                        <input type="text" name="cost" value="<?=$entrys['cost']; ?>" >
                    </td>
                    <td>
                        <input type="text" name="savings_total" value="<?=$entrys['savings_total']; ?>" >
                    </td>
                    <td>
                        <input type="text" name="points_earned" value="<?=$entrys['points_earned']; ?>" >
                    </td>
                </tr>
                <tr class="tbl_header">
                    <th>Points Spent</th>
                    <th>Dated of Purchased</th>
                    <th>Purchaser</th>
                    <th>Method Of Payment</th>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="points_spent" value="<?=$entrys['points_spent']; ?>" >
                    </td>
                    <td>
                        <input id="datepicker1" type="date" name="time_purchased" value="<?=date('Y-m-d', strtotime(str_replace('-', '/', htmlentities($entrys['time_purchased'])))); ?>"  >
                    </td>
                    <td>
                        <input type="text" name="purchaser" value="<?=$entrys['purchaser']; ?>" >
                    </td>
                    <td>
                        <input type="text" name="method_of_payment" value="<?=$entrys['method_of_payment']; ?>" >
                    </td>
                </tr>

            </table>
        </div>
        <div>
            <table border="0" style="margin: 0 auto;" id="receipt-items">
                <?php

                $stripe = false;
                foreach ($items as $item) {
                    // Shade every 2nd line ?>
                    <tr class="tbl_header">
                        <th>Item Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Size</th>
                    </tr>
                    <?php
                    $stripe = !$stripe;
                    $time_stamp = date('Y-m-d', strtotime(str_replace('-', '/', htmlentities($item['time_stamp_purchased']))));
                    if ($stripe) { ?>
                        <tr class="odd" data-i_id="<?=$item['id']; ?>">
                    <?php } else { ?>
                        <tr class="even" data-i_id="<?=$item['id']; ?>">
                    <?php } ?>

                    <td><label></label><?=$item['name']; ?></label></td>
                    <td><input type="text" name="price-<?=$item['id']; ?>" value="<?=$item['price']; ?>" placeholder="Price" style="width: 50%;"></td>
                    <td><input type="text" name="cate-<?=$item['id']; ?>" value="<?=$item['category']; ?>" placeholder="Category" style="width: 50%;"></td>
                    <td><input type="text" name="type-<?=$item['id']; ?>" value="<?=$item['type']; ?>" placeholder="Type" style="width: 50%;"></td>
                    <td><input type="text" name="size-<?=$item['id']; ?>" value="<?=$item['size']; ?>" placeholder="Size" style="width: 50%;">
                        <input type="text" name="size_unit-<?=$item['id']; ?>" value="<?=$item['size_unit']; ?>" placeholder="Unit" style="width: 50%;"></td>
                    </tr>
                    <tr class="tbl_header">
                        <th>Number Purchased</th>
                        <th>Savings</th>
                        <th>Brand</th>
                        <th>Date Purchased</th>
                        <th>Action</th>
                    </tr>
                    <?php if ($stripe) { ?>
                        <tr class="odd" data-i_id="<?=$item['id']; ?>">
                    <?php } else { ?>
                        <tr class="even" data-i_id="<?=$item['id']; ?>">
                    <?php } ?>
                    <td><input type="text" name="amount-<?=$item['id']; ?>" value="<?=$item['amount']; ?>" placeholder="Amount Purchased" style="width: 50%;"></td>
                    <td><input type="text" name="savings-<?=$item['id']; ?>" value="<?=$item['savings']; ?>" placeholder="Savings" style="width: 50%;"></td>
                    <td><input type="text" name="brand-<?=$item['id']; ?>" value="<?=$item['brand']; ?>" placeholder="Brand" style="width: 93%;"></td>
                    <td><input type="date" name="time_stamp_purchased-<?=$item['id']; ?>" value="<?=$time_stamp; ?>" style="width: 92%;"></td>
                    <td><input type="button" name="delete" value="Delete" data-id="<?=$item['receipt_id']; ?>"></td>
                    </tr>
                    <tr></tr>
                    <tr></tr>
                    <tr></tr>

                <?php } ?>
            </table>
        </div>
        <input type="submit" name="edit" value="Update Receipt" /><input type="submit" name="delete" value="Delete Receipt" />


    </form>
</div>