<?php
include 'config.php';
include 'opendb.php';
require_once "link.php";
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
     $query .= (!empty($items_id)? 			"`num_items` = '" 			.  $conn->real_escape_string(count($items_id)) . "', " : "");
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
	    		item += "<input type=\"text\" name='"+val+"_type[]' placeholder=\"Type\">";
	    		item += "<input type=\"text\" type=\"text\" name='"+val+"_cate[]' placeholder=\"Category\"></td></tr>";
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
       			    items += "<input type=\"text\" name='"+val[key]+"_type[]' placeholder=\"Type\">";
       			    items += "<input type=\"text\" name='"+val[key]+"_cate[]' placeholder=\"Category\"></td></tr>";
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

<?php
include 'closedb.php';
include('html/add.html.php');
include 'footer.html';
?>