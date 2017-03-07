<?php
session_start(); //start session
include 'config.inc.php'; //include config file
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Ismael's internet shop</title>
<link href="style/style.css" rel="stylesheet" type="text/css">
<script  src="js/jquery-1.11.2.min.js"></script>

</head>
<body>
<div align="center">
<h3>Internet Shop</h3>
</div>

<a href="#" class="cart-box" id="cart-info" title="View Cart">
<?php
if (isset($_SESSION['products'])) {
    echo count($_SESSION['products']);
} else {
    echo 0;
}
?>
</a>

<div class="shopping-cart-box">
<a href="#" class="close-shopping-cart-box" >Close</a>
<h3>Your Shopping Cart</h3>
    <div id="shopping-cart-results">
    </div>
</div>

<?php
//List products from database
$results = $mysqli_conn->query('SELECT * FROM products');
//Display fetched records as you please

$products_list = '<ul class="products-wrp">';

while ($row = $results->fetch_assoc()) {
    $products_list .= <<<EOT
<li>
<form class="form-item">
<h4>{$row['product_name']}</h4>
<div><img src="images/{$row['product_image']}"></div>
<div>Price : USD {$row['product_price']}<div>

<div>Rating :  {$row['current_rating']}<div>
<div class="item-box">
    <div>

	<div>
    Qty :
    <select name="product_qty">
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
    </select>
	</div>

	<div>
    Rate :
    <select name="curent_rate">
  <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
    </select>

	</div>

    <input name="product_code" type="hidden" value="{$row['product_code']}">
    <button type="submit">Add to Cart</button>
</div>
</form>
</li>
EOT;
}
$products_list .= '</ul></div>';

echo $products_list;
?>

<div class="transport">
	 <h3>choose a transport type</h3>
	 <form class="" action="index.html" method="post">

			<input type="radio" id="pickup" checked class="transport-type" name="transport" value="0">
			<label for="pickup">pick up ($0)</label>

			<input type="radio" id="pickup" class="transport-type" name="transport" value="5">
			<label for="pickup">UPS ($5)</label>
	 </form>
</div>

<script>
$(document).ready(function(){

		$('body').find(".form-item").submit(function(e){
			var form_data = $(this).serialize();
			var button_content = $(this).find('button[type=submit]');
			button_content.html('Adding...');

			$.ajax({
				url: "cart_process.php",
				type: "POST",
				dataType:"json",
				data: form_data
			}).done(function(data){ //on Ajax success
				$("#cart-info").html(data.items); //total items in cart-info element
				button_content.html('Add to Cart'); //reset button text to original text
				alert("Item added to Cart!"); //alert user
				if($(".shopping-cart-box").css("display") == "block"){ //if cart box is still visible
					$(".cart-box").trigger( "click" ); //trigger click to update the cart box.
				}
			})
			e.preventDefault();
		});

	//Show Items in Cart
	$( ".cart-box").click(function(e) {
		e.preventDefault();
		$(".shopping-cart-box").fadeIn(); //display cart box
		$("#shopping-cart-results").html('<img src="images/ajax-loader.gif">');
		$("#shopping-cart-results" ).load( "cart_process.php", {"load_cart":"1"});
	});

	//Close Cart
	$( ".close-shopping-cart-box").click(function(e){
		e.preventDefault();
		$(".shopping-cart-box").fadeOut(); //close cart-box
	});

	//Remove items from cart
	$("#shopping-cart-results").on('click', 'a.remove-item', function(e) {
		e.preventDefault();
		var pcode = $(this).attr("data-code"); //get product code
		$(this).parent().fadeOut(); //remove item element from box
		$.getJSON( "cart_process.php", {"remove_code":pcode} , function(data){
			$("#cart-info").html(data.items);
			$(".cart-box").trigger( "click" );
		});
	});

	//update transport means

	 $('.transport-type').on('change',function(){
		  $selected = $(this).filter(':checked');
			$.post('cart_process.php',{cost:$selected.val()}).done(function(){
				alert('transport type updated');
			});
	 });



   });

</script>
</body>
</html>
