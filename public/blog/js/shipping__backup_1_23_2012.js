function getVal(el) {
	var toRet = parseFloat($(el).val());
	if(isNaN(toRet)) {
		toRet = 0;
	}
       	return toRet * 100;
}
function emitVal(quant) {
	return (quant / 100).toFixed(2);
}
function getPrice(ShippingType, ShippingValue, SourceZip, DestZip, Weight, Country){	
	function errorHandler(){
		alert("There is an error in your shipping address, please correct the error and try again.");
		history.go(-1);
	}

	$.ajax({
		type: "POST",
		url: 'shippingCost.php',
		data: [	"Shipping=" + ShippingType,
			"ShippingValue=" + ShippingValue,
			"Weight=" + Weight,
			"SourceZip=" + SourceZip,
			"DestZip=" + DestZip,
			"Country=" + Country
		].join('&'),

		async: false,
		success: function(html) { 	
			//so, if data is retrieved, store it in html
			if(Weight > 0 && html==""){
				errorHandler();
			} 

			var 	ShippingCost = parseInt(html.replace('.', ''));

			if(isNaN(ShippingCost)) {
				errorHandler();
			}

			if(ShippingType == 'USPS') {
				ShippingCost += 150;
			} 

			var 	SubTotal = getVal('#SubTotal'),
				Tax = getVal('#Tax'),
				ProdDiscount = getVal('#ProdDiscout'),
				TotalCost = (SubTotal + Tax + ShippingCost) - ProdDiscount;

			$("#ShippingCostTd").html("$" + emitVal(ShippingCost));
			$("#TotalCostTd").html("$" + emitVal(TotalCost));
			$('#Shipping').val( emitVal(ShippingCost));
			$('#TotalAmount').val( emitVal(TotalCost));

			if(document.getElementById('TotalCostWithoutDiscTd')){
				$("#TotalCostWithoutDiscTd").html("$"+ emitVal(TotalCost));
			}

			if(document.getElementById('ShippingType')){
				$('#ShippingType').val(ShippingType);
				if(ShippingValue == 'STANDARDOVERNIGHT'){
					$('#ShippingValue').val('STANDARD OVERNIGHT');
				}else if(ShippingValue == 'INTERNATIONALECONOMY'){
					$('#ShippingValue').val('INTERNATIONAL ECONOMY');
				}else{
					$('#ShippingValue').val(ShippingValue);
				}
				
			}
		}
	});
}

function changeShipping(ShippingType){
	if(ShippingType == 'USPS'){
		document.getElementById(ShippingType+'_DIV').style.display = '';
		document.getElementById('FEDEX_DIV').style.display = 'none';
	}else{
		document.getElementById(ShippingType+'_DIV').style.display = '';
		document.getElementById('USPS_DIV').style.display = 'none';
	}
	
}
