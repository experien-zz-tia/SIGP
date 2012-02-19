
var AppGen = {

	getAttributes: function(element){
		var table = $F(element);
		new Ajax.Request(Utils.getKumbiaURL("create/getFields"), {
			parameters: {
				table: table
			},
			onSuccess: function(transport){
				var fields = transport.responseText.evalJSON();
				var html = "";
				fields.each(function(field){
					html+="<option value='"+field+"'>"+field+"</option>";
				});
				$("fieldRelation").innerHTML = "";
				$("fieldRelation").update(html);
				$("fieldOrder").innerHTML = "";
				$("fieldOrder").update(html);
				$("fieldDetail").innerHTML = "";
				$("fieldDetail").update(html);
			}
		});
	},

	addRelation: function(){
		if($F("campo")=="@"){
			alert("Debe seleccionar un campo");
			return;
		}
		if($F("tableRelation")=="@"){
			alert("Debe seleccionar una tabla relación");
			return;
		}
		if($F("fieldRelation")=="@"||$("fieldRelation").options.length==0){
			alert("Debe seleccionar un campo relación");
			return;
		}
		new Ajax.Request(Utils.getKumbiaURL("create/addRelation"), {
			parameters: {
				tableRelation: $F("tableRelation"),
				fieldRelation: $F("fieldRelation"),
				fieldOrder: $F("fieldOrder"),
				fieldDetail: $F("fieldDetail"),
				field: $F("campo")
			},
			onSuccess: function(transport){
				$("relationDiv").update(transport.responseText);
			}
		});
	}

};



if($Kumbia.action=="index"){
	if($F("extappname")=="@"){
		$("newappname").enable();
		$("newappname").activate();
	}
	$("extappname").observe("change", function(){
		if($F("extappname")=="@"){
			$("newappname").enable();
			$("newappname").activate();
		} else {
			$("newappname").disable();
		}
	});
	$("newappname").activate();
}

if($Kumbia.action=="nueva"){
	$("resumen").activate();
}