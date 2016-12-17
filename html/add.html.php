<div class="colmask" style="margin-left: 5%; margin-right: 5%;">
    <form action="?submit" method="post">
        <div class="header">
        </div>
        <div class="colmid">
            <div class="colleft">
                <div class="col1">
                    <fieldset>
                        <label>Date of Purchased:</label><br/>
                        <input id="datepicker" type="date" name="time_purchased" required>
                    </fieldset>

                    <fieldset>
                        <label>Subtotal:</label><br/>
                        <input type="text" name="cost_before_tax">
                    </fieldset>

                    <fieldset>
                        <label>PST:</label><br/>
                        <input type="text" name="pst">
                    </fieldset>

                    <fieldset>
                        <label>Points Spent:</label><br/>
                        <input type="text" name="points_spent">
                    </fieldset>

                    <fieldset>
                        <label>Savings Total:</label><br/>
                        <input type="text" name="savings_total">
                    </fieldset>
                </div>
                <div class="col2">
                    <fieldset>
                        <label>Location:</label><br/>
                        <input type="text" name="location" required style="width: 97%;">
                    </fieldset>

                    <fieldset>
                        <label>Type:</label><br/>
                        <input type="text" name="receipt_type" required style="width: 97%;">
                    </fieldset>

                    <fieldset>
                        <label>Enter Items:</label><button id="item-manage" style="float: right;">Manage</button><br/>
                        <input type="text" name="items_id" list="list-item" value="" style="width: 97%;">
                        <label id="none" hidden>No Items Found</label>
                        <datalist id="list-item">
                        </datalist>
                    </fieldset>

                    <fieldset style="border-style:solid;">
                        <table class="item-list" id="item-list"></table>
                    </fieldset>

                    <fieldset class="colw2">
                        <label>Cashier:</label><br/>
                        <input type="text" name="cashier" style="width: 97%;">
                    </fieldset>
                </div>
                <div class="col3">
                    <fieldset>
                        <label>Purchaser:</label><br/>
                        <input type="text" name="purchaser" required>
                    </fieldset>

                    <fieldset>
                        <label>Cost:</label><br/>
                        <input type="text" name="cost" required>
                    </fieldset>

                    <fieldset>
                        <label>GST:</label><br/>
                        <input type="text" name="gst">
                    </fieldset>

                    <fieldset>
                        <label>Points Earned:</label><br/>
                        <input type="text" name="points_earned">
                    </fieldset>

                    <fieldset>
                        <label>Method of Payment:</label><br/>
                        <select name="method_of_payment" style="width: 100%;">
                            <option value="Credit">Credit</option>
                            <option value="Debit">Debit</option>
                            <option value="Cash">Cash</option>
                            <option value="Other">Other</option>
                        </select>
                    </fieldset>
                </div>
            </div>
        </div>
        <div class="footer">
            <input type="submit" name="add" value="Add Receipt" />
        </div>
    </form>
</div>
<div id="add-item" style="display: none; margin-left: 42%; margin-right: 58%; top: -240px;">
    <input type="button" id="close" value="X" />
    <iframe src="<?=$link_url; ?>/add-item" style="width: 245px; height: 270px; border-radius: 20px; border: 2px solid #000;"></iframe>
</div>