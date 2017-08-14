function getVal(el) {
	/*
	var toRet = parseFloat($(el).val());
	if(isNaN(toRet)) {
		toRet = 0;
	}
       	return toRet * 100;
       	*/
       	return parseFloat($(el).val());
}
function emitVal(quant) {
	//return (quant / 100).toFixed(2);
	return quant.toFixed(2);
}
function getPrice(ShippingType, ShippingValue, SourceZip, DestZip, Weight, Country){	
	function errorHandler(){
		alert("There is an error in your shipping address, please correct the error and try again.");
		history.go(-1);
	}

	$.ajax({
		type: "POST",
		url: 'shippingCost.php',
		data: [	"Shipping=" + encodeURIComponent(ShippingType),
			"ShippingValue=" + encodeURIComponent(ShippingValue),
			"Weight=" + encodeURIComponent(Weight),
			"SourceZip=" + encodeURIComponent(SourceZip),
			"DestZip=" + encodeURIComponent(DestZip),
			"Country=" + encodeURIComponent(Country)
		].join('&'),

		async: false,
		success: function(html) { 	
			//so, if data is retrieved, store it in html
			if(parseFloat(Weight) > 0 && html==""){
				//~ alert('html2');
				errorHandler();
			} 

			var 	ShippingCost = parseFloat(html+"");

			if(isNaN(ShippingCost)) {
				//~ alert('html1');
				
//~ top.consoleRef=window.open('','myconsole',
  //~ 'width=350,height=250'
   //~ +',menubar=0'
   //~ +',toolbar=1'
   //~ +',status=0'
   //~ +',scrollbars=1'
   //~ +',resizable=1')
 //~ top.consoleRef.document.writeln(
  //~ '<html><head><title>Console</title></head>'
   //~ +'<body bgcolor=white onLoad="self.focus()">'
   //~ +html
   //~ +'</body></html>'
 //~ )
 //~ top.consoleRef.document.close()
				//~ alert(html);
				errorHandler();
			}

			if(ShippingType == 'USPS') {
				ShippingCost += 1.50;
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
