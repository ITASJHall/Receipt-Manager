<?php
include 'config.php';
include 'opendb.php';
include 'header.html'; 
?>

<?php
 if (isset($_POST['add'])){

	unset($_POST['add']);
	$add_array = array('location', 'cost', 'cost_before_tax', 'pst', 'gst', 'method_of_payment', 'points_spent', 'receipt_type', 'points_earned', 'savings_total', 'cashier', 'purchaser', 'time_purchased', 'items_id');
	foreach($_POST as $key=>$value){
		if(in_array($key, $add_array)){
			$$key = $value;
			unset($_POST[$key]);
		}
	}
	$items = array();
	foreach($_POST as $key=>$value){
		if(!strstr($key, '_')){
            $items[$key] = array();
            for($i=0; $i < count($value); $i++){
                $items[$key][] = array(
                    'category'  =>	$_POST[$key.'_cate'][$i],
                    'type'    	=>	$_POST[$key.'_type'][$i],
                    'price'     =>	$_POST[$key.'_price'][$i],
                    'size'      =>	$_POST[$key.'_size'][$i],
                    'unit'	    =>	$_POST[$key.'_unit'][$i],
                    'amount'    =>	$_POST[$key.'_amount'][$i],
                    'savings'   =>	$_POST[$key.'_save'][$i],
                    'brand'     =>	$_POST[$key.'_brand'][$i]
                );
            }

		}
	}
	$items_id = array();
	foreach($items as $item => $values){
		$result = mysqli_query($conn,"SELECT `id` FROM  `_items` WHERE `name` = '" . $item . "'");	
		if($result){
			$id = $result->fetch_assoc()['id'];
			foreach($values as $info) {
				$info['category'] = (!empty($info['category'])? $info['category'] : $receipt_type);
				$query = "INSERT INTO `_items_purchased` SET ";
				$query .= (!empty($info['category']) ? "`category` = '" . $conn->real_escape_string(htmlentities($info['category'])) . "', " : "");
				$query .= (!empty($info['type']) ? "`type` = '" . $conn->real_escape_string(htmlentities($info['type'])) . "', " : "");
				$query .= (!empty($info['price']) ? "`cost_per_unit` = '" . $conn->real_escape_string(htmlentities($info['price'])) . "', " : "");
				$query .= (!empty($info['size']) ? "`size` = '" . $conn->real_escape_string(htmlentities($info['size'])) . "', " : "");
				$query .= (!empty($info['unit']) ? "`size_unit` = '" . $conn->real_escape_string(htmlentities($info['unit'])) . "', " : "");
				$query .= (!empty($info['amount']) ? "`amount` = '" . $conn->real_escape_string(htmlentities($info['amount'])) . "', " : "");
				$query .= (!empty($info['savings']) ? "`savings` = '" . $conn->real_escape_string(htmlentities($info['savings'])) . "', " : "");
				$query .= (!empty($id) ? "`item_id` = '" . $conn->real_escape_string($id) . "', " : "");
				$query .= (!empty($info['brand']) ? "`brand` = '" . $conn->real_escape_string(htmlentities($info['brand'])) . "', " : "");
				$query .= "`time_stamp_updated` = NOW(), ";
				$query .= "`time_stamp_created` = NOW();";
				if (mysqli_query($conn, $query)) {
					$items_id[] = mysqli_insert_id($conn);
				} else {
					echo mysqli_error($conn);
				}
			}
		}
	}
 	
 	//format date
     $date = date('Y-m-d', strtotime(str_replace('-', '/', htmlentities($time_purchased))));
     
     $query = "INSERT INTO `_receipts` SET ";
     $query .= (!empty($location)? 			"`location` = '" 			.  $conn->real_escape_string(htmlentities($location)) . "', " : ""); 
     $query .= (!empty($receipt_type)? 		"`type` = '" 				.  $conn->real_escape_string(htmlentities($receipt_type)) . "', " : ""); 
     $query .= (!empty($cost)? 				"`cost` = '" 				.  $conn->real_escape_string(htmlentities($cost)) . "', " : ""); 
     $query .= (!empty($cost_before_tax)? 	"`cost_before_tax` = '" 	.  $conn->real_escape_string(htmlentities($cost_before_tax)) . "', " : ""); 
     $query .= (!empty($pst)? 				"`pst` = '" 				.  $conn->real_escape_string(htmlentities($pst)) . "', " : ""); 
     $query .= (!empty($gst)? 				"`gst` = '" 				.  $conn->real_escape_string(htmlentities($gst)) . "', " : ""); 
     $query .= (!empty($method_of_payment)? "`method_of_payment` = '" 	.  $conn->real_escape_string(htmlentities($method_of_payment)) . "', " : ""); 
     $query .= (!empty($date)? 				"`time_purchased` = '" 		.  $conn->real_escape_string($date) . "', " : ""); 
     $query .= (!empty($points_spent)? 		"`points_spent` = '" 		.  $conn->real_escape_string(htmlentities($points_spent)) . "', " : ""); 
     $query .= (!empty($points_earned)? 	"`points_earned` = '" 		.  $conn->real_escape_string(htmlentities($points_earned)) . "', " : ""); 
     $query .= (!empty($savings_total)? 	"`savings_total` = '" 		.  $conn->real_escape_string(htmlentities($savings_total)) . "', " : ""); 
     $query .= (!empty($cashier)? 			"`cashier` = '"				.  $conn->real_escape_string(htmlentities($cashier)) . "', " : ""); 
     $query .= (!empty($purchaser)? 		"`purchaser` = '" 			.  $conn->real_escape_string(htmlentities($purchaser)) . "', " : ""); 
     $query .= "`time_stamp_updated` = NOW(), ";
     $query .= "`time_stamp_created` = NOW();";
	 
	 
     if(mysqli_query($conn, $query)){
     	
     	$receipt_id = mysqli_insert_id($conn);
		
		foreach($items_id as $id){
			$query = "UPDATE `_items_purchased` SET `receipt_id` = '" . $receipt_id . "', `time_stamp_purchased` = '" . $date . "' WHERE `id` = '" . $id . "';";
			
			if(mysqli_query($conn, $query)){
			 } else {
			 	echo mysqli_error($conn);
			 }
		}
		
		$receipt_update = mysqli_query($conn,"SELECT COUNT(*) as `total`, SUM(`savings`) as `savings` FROM  `_items_purchased` WHERE `receipt_id` = '" . $receipt_id . "';");		
		
		if($receipt_update) {		
			$update = $receipt_update->fetch_assoc();		
			
			if(!empty($update['savings'])){
				$query = "UPDATE `_receipts` SET `savings_total` = IF(`savings_total` IS NULL,'" . $update['savings'] . "', `savings_total`), `num_items` = '" . $update['total'] . "' WHERE `id` = '" . $receipt_id . "';";
				
				if(mysqli_query($conn, $query)){					
				} else {
					echo mysqli_error($conn);
				}
			}
		}
		
				
		$item_update = mysqli_query($conn,"SELECT `item_id` FROM  `_items_purchased` WHERE `receipt_id` = '" . $receipt_id . "';");	
		
		if($item_update){
			while($item_purchased = $item_update->fetch_assoc()) {
				
				$item_stats = array();				
					
				$item_count_query = mysqli_query($conn,"SELECT SUM(`amount`) as `total` FROM  `_items_purchased` WHERE `item_id` = '" . $item_purchased['item_id'] . "';");	
				
				$item_cost_query = mysqli_query($conn,"SELECT SUM(`cost_per_unit`) as `cost` FROM  `_items_purchased` WHERE `item_id` = '" . $item_purchased['item_id'] . "';");
				
				$item_avgcost_query = mysqli_query($conn,"SELECT AVG(`cost_per_unit` / `amount`) as `avg_cost` FROM  `_items_purchased` WHERE `item_id` = '" . $item_purchased['item_id'] . "';");
					
				$item_time_query = mysqli_query($conn,"SELECT IFNULL(TIMESTAMPDIFF(DAY, MIN(`time_stamp_purchased`), MAX(`time_stamp_purchased`)) / NULLIF(COUNT(DISTINCT `time_stamp_purchased`) - 1,0),0) as `frequency` FROM  `_items_purchased` WHERE `item_id` = '" . $item_purchased['item_id'] . "';");

				$item_stats['total'] = $item_count_query->fetch_assoc()['total'];
				$item_stats['cost'] = $item_cost_query->fetch_assoc()['cost'];
				$item_stats['avgcost'] = $item_avgcost_query->fetch_assoc()['avg_cost'];
				$item_stats['time'] = $item_time_query->fetch_assoc()['frequency'];
				
				$query = "UPDATE `_items` SET ";
                $query .= !empty($item_stats['total'])? "`number_purchased` = '" . $item_stats['total'] . "'," : "";
				$query .= !empty($item_stats['cost'])?"`spent_total` = '" . $item_stats['cost'] . "'," : "";
				$query .= !empty($item_stats['avgcost'])?"`cost_avg` = '" . $item_stats['avgcost'] . "'," : "";
				$query .= !empty($item_stats['time'])?"`frequency` = '" . $item_stats['time'] . "'" : "";
				$query .=" WHERE `id` = '" . $item_purchased['item_id'] . "';";
			
				if(mysqli_query($conn, $query)){
					
				} else {
					echo mysqli_error($conn);
				}
				
				
			}
		}
		
        
     }  else {
	 	echo mysqli_error($conn);
	 }   
}

?>  
  <script>
  $(document).ready(function( )    {    	
				
       $("#item-manage").click(function(e)   { 
	       e.preventDefault(); 
	       $('.colmask').addClass('back');
	       $('#add-item').show();
	       $('#add-item').draggable();
       });
       
       $("#close").click(function(e)   { 	       
	       e.preventDefault(); 
	       $('.colmask').removeClass('back');
	       $('#add-item').hide();
       });
             
       $("input[name='items_id']").on('input',function(){       	
       	var search = $(this).val();
       	var opts  = $('#list-item').children();
       	var selected = false;
       	for (var i=0; i<opts.length; i++){       		
       		if(opts[i].value === search) {
       			selected = true;
       			addItem(opts[i].value);
       			$("input[name='items_id']").val('');       			
       			$('#list-item').children().remove();      			
       		}
       	}
       	
       	if (search.length >= 2 && !selected){
       		$.ajax({
       			method:'POST',
       			url:'item.php',
       			data: { 'search': search       				
       			}
       		}).done(function(opt, statusText, xhr){
       			var status = xhr.status;       			
       			if(status == 200){
       				$('#list-item').html(opt);
       			} else {
       				$('#none').show();
       				setTimeout(function(){
       					$('#none').fadeOut();
       				}, 1000);  				
       			}
       		});
       	}
       	
       }).blur(function(){
		   if($(this).val().length <= 1) {
			   $('#list-item').children().remove();
		   }
	   });
       function addItem(val){  
       		var item = "<tr><td><input type=\"checkbox\" name='"+val+"[]' value='"+val+"' checked><label>"+val+"</label>";
	    		item += "<input style=\"margin-right: 19.5%; float:right;\" type=\"text\" name='"+val+"_type[]' placeholder=\"Type\">";
	    		item += "<input style=\"float:right;\" type=\"text\" type=\"text\" name='"+val+"_cate[]' placeholder=\"Category\"></td></tr>";
	    		item += "<tr><td><input type=\"text\" name='"+val+"_price[]' placeholder=\"Price\">";
			    item += "<input type=\"text\" name='"+val+"_size[]' placeholder=\"Weight/Size\">";
			    item += "<input type=\"text\" name='"+val+"_unit[]' placeholder=\"Unit\"></td></tr>";
	    		item += "<tr><td><input type=\"text\" name='"+val+"_amount[]' placeholder=\"Amount\">";
	        	item += "<input type=\"text\" name='"+val+"_save[]' placeholder=\"Savings\">";
			    item += "<input type=\"text\" name='"+val+"_brand[]' placeholder=\"Brand\"></td></tr>";
       		// var item = "<li><input type='checkbox' name='"+val+"' value='"+val+"' checked><label> "+val+" </label><input type='text' name='"+val+"price'></li>";
       		$("#item-list").append(item);       	
       }       

      });
      function addItems(val){  
       		for (var key in val){
       			var items = "<tr><td><input type=\"checkbox\" name='"+val[key]+"[]' value='"+val[key]+"' checked><label>"+val[key]+"</label>";
       			    items += "<input style=\"margin-right: 19.5%; float:right;\" type=\"text\" name='"+val[key]+"_type[]' placeholder=\"Type\">";
       			    items += "<input style=\"float:right;\" type=\"text\" name='"+val[key]+"_cate[]' placeholder=\"Category\"></td></tr>";
	        		items += "<tr><td><input type=\"text\" name='"+val[key]+"_price[]' placeholder=\"Price\">";
				    items += "<input type=\"text\" name='"+val[key]+"_size[]' placeholder=\"Weight/Size\">";
				    items += "<input type=\"text\" name='"+val[key]+"_unit[]' placeholder=\"Unit\"></td></tr>";
	        		items += "<tr><td><input type=\"text\" name='"+val[key]+"_amount[]' placeholder=\"Amount\">";
		        	items += "<input type=\"text\" name='"+val[key]+"_save[]' placeholder=\"Savings\">";
				    items += "<input type=\"text\" name='"+val[key]+"_brand[]' placeholder=\"Brand\"></td></tr>";
       			// var items = "<li><input type='checkbox' name='"+val[key]+"' value='"+val[key]+"' checked><label> "+val[key]+" </label><input type='text' name='"+val[key]+"price'></li>";
       			$("#item-list").append(items);
       		}       	
      }
  
  $(function() {
    $( "#datepicker" ).datepicker();
  });
	
	
  </script>
  
  <div class="colmask" style="margin: auto;">
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
					<!-- <ul class="item-list" style="list-style-type: none;">
			        	<li><input type="checkbox" name="items" value="item1"><label> Item1 </label></br>
			        		<input type="text" name="items1price" placeholder="Price">
			        		<input type="text" name="items1price" placeholder="Weight/Size">
			        		<input type="text" name="items1price" placeholder="Unit">
			        		<input type="text" name="items1price" placeholder="Amount">
			        		<input type="text" name="items1price" placeholder="Savings">
			        		<input type="text" name="items1price" placeholder="Brand">
			        	</li>       		
			        	<li><input type="checkbox" name="items" value="item2"><label> Item2 </label></br>
			        		<input type="text" name="items2price" placeholder="Price">
			        		<input type="text" name="items2price" placeholder="Weight/Size">
			        		<input type="text" name="items2price" placeholder="Unit">
			        		<input type="text" name="items2price" placeholder="Amount">
			        		<input type="text" name="items2price" placeholder="Savings">
			        		<input type="text" name="items2price" placeholder="Brand">
			        	</li>       		
	        		</ul> -->
	        		<table class="item-list" id="item-list">
	        			<!-- <tr>
	        				<td><input type="checkbox" name="items" value="item1"><label>Item1</label></td>
	        			</tr>
	        			<tr>
	        				<td>
		        				<input type="text" name="items1price" placeholder="Price">
				        		<input type="text" name="items1price" placeholder="Weight/Size">	
				        		<input type="text" name="items1price" placeholder="Unit">			        		
			        		</td>
	        			</tr>
	        			<tr>
	        				<td>	        					
		        				<input type="text" name="items1price" placeholder="Amount">	
		        				<input type="text" name="items1price" placeholder="Savings">
				        		<input type="text" name="items1price" placeholder="Brand">			        		
			        		</td>
	        			</tr> --> 	        			       			
	        		</table>	    	    
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
<iframe src="/add-item" style="width: 245px; height: 270px; border-radius: 20px; border: 2px solid #000;"></iframe>
</div>
<?php
include 'closedb.php';
include 'footer.html';
?>