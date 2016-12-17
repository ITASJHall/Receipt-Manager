<?php if(!empty($item)) { ?>
<script>
    $(function () {
        $("#datepicker1").datepicker();
    });
</script>
<?php if(!empty($errors)){ ?>
    <div class="error"><?=implode(', ',$errors); ?></div>
<?php } elseif(!empty($successes)){ ?>
    <div class="success"><?=implode(', ',$successes); ?></div>
<?php } ?>
<?php $time_stamp = date('Y-m-d', strtotime(str_replace('-', '/', htmlentities($item['time_stamp_purchased'])))); ?>
<div class="colmask-2" style="margin-left: 5%; margin-right: 5%;">
    <form action="?ID=<?= $entry_id ?>" method="post" style="text-align: center;">
        <div>
            <table border="0" style="margin: 0 auto;">
                <tr class="tbl_header">
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Size</th>
                </tr>
                <tr class="odd" data-i_id="<?=$item['id']; ?>">
                    <td><label></label><?=$item['name']; ?></label></td>
                    <td><input type="text" name="price" value="<?=$item['price']; ?>" placeholder="Price" style="width: 50%;"></td>
                    <td><input type="text" name="category" value="<?=$item['category']; ?>" placeholder="Category" style="width: 50%;"></td>
                    <td><input type="text" name="type" value="<?=$item['type']; ?>" placeholder="Type" style="width: 50%;"></td>
                    <td><input type="text" name="size" value="<?=$item['size']; ?>" placeholder="Size" style="width: 50%;">
                        <input type="text" name="size_unit" value="<?=$item['size_unit']; ?>" placeholder="Unit" style="width: 50%;"></td>
                </tr>
                <tr class="tbl_header">
                    <th>Number Purchased</th>
                    <th>Savings</th>
                    <th>Brand</th>
                    <th>Date Purchased</th>
                    <th>Actions</th>
                </tr>
                <tr class="odd" data-i_id="<?=$item['id']; ?>">
                    <td><input type="text" name="amount" value="<?=$item['amount']; ?>" placeholder="Amount Purchased" style="width: 50%;"></td>
                    <td><input type="text" name="savings" value="<?=$item['savings']; ?>" placeholder="Savings" style="width: 50%;"></td>
                    <td><input type="text" name="brand" value="<?=$item['brand']; ?>" placeholder="Brand" style="width: 93%;"></td>
                    <td><input id="datepicker1" type="date" name="time_stamp_purchased" value="<?=$time_stamp; ?>" style="width: 92%;"></td>
                    <td><input type="submit" name="delete" value="Delete"></td>
                </tr>
            </table>
        </div>
        <input type="submit" name="edit" value="Update Item"/>


    </form>
</div>
<?php } ?>