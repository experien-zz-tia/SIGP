frmTutorEmpresarial = Ext.extend(frmTutorEmpresarialUi, {
    initComponent: function() {
    	frmTutorEmpresarial.superclass.initComponent.call(this);
    	habilitarCampos(false);
        Ext.getCmp('btnLimpiar').on('click',this.cancelar);
        Ext.getCmp('btnSalir').on('click',this.salir);
        Ext.getCmp('btnGuardar').on('click',this.guardarTutor);
    	Ext.getCmp('txtCedula').on('blur',this.buscarTutor);

    },
     salir:function(){
    	 Ext.getCmp('formTutorEmpresarial').getForm().reset();
    	 Ext.getCmp('frmTutorEmpresarial').close();  
    },
    cancelar:function(){
    	 Ext.getCmp('formTutorEmpresarial').getForm().reset();
    	 Ext.getCmp('txtCedula').enable();
    	 habilitarCampos(false);
    },
     guardarTutor:function(){
 // Se verifica que los campos marcados como obligatorios
	// (allowBlank:false) esten llenos
 if (Ext.getCmp('txtCedula').disabled){
	if (Ext.getCmp('formTutorEmpresarial').getForm().isValid()){
		Ext.getCmp('formTutorEmpresarial').getForm().submit({ waitMsg : 'Enviando datos...', 
			 												params:{ txtCedula:  Ext.getCmp('txtCedula').getValue()
		 															}, 
			                                                failure: function (form, action){
                                  	                                   Ext.MessageBox.show({  
		                                    	                        title: 'Error',  
		                                    	                        msg: 'Error al registrar.',  
		                                    	                        buttons: Ext.MessageBox.OK,  
		                                    	                        icon: Ext.MessageBox.ERROR  
		                                    	                      });  
		                                    	                     },  
		                                                    success: function (form, action){                                              
		                                    	                    		Ext.MessageBox.show({  
				                                      	                        title: 'Informaci&oacute;n',  
				                                      	                        msg: 'Registro exitoso.',  
				                                      	                        buttons: Ext.MessageBox.OK,  
				                                      	                        icon: Ext.MessageBox.INFO,
				                                      	                        fn: function (){
				                                    	                    	  Ext.getCmp('formTutorEmpresarial').getForm().reset();
				                                    	                    	  Ext.getCmp('frmTutorEmpresarial').close();    
				                                    	                    	  stTutoresEmpresariales.reload();
				                                    	                       	}
				                                      	                       });
		                                    	                     }  //End Success
			                                                    }); 
	
		}else{
			Ext.MessageBox.show({
		     title: "Error",
		     msg: "Datos incompletos o no v&aacute;lidos, por favor verifique.",
		     width:400,
		     buttons: Ext.MessageBox.OK,
		     icon: Ext.MessageBox.ERROR
		    });
	  }
	 }
},	
buscarTutor:function(){
	/* Se busca la cedula ingresada, en caso de encontrarla se pregunta si quiere modificar 
	* sus datos, en caso afirmativo se terminan de cargar los datos y se deshabilota la caja de texto
	* , en caso negativo, se limpia la caja de texto de la cedula. En el caso de no encontrar la cedula se 
	* deshabilita la cedula y se permite q ingrese el resto de los datos
	*/ 
	
	var cedula = Ext.getCmp('txtCedula');
	if (cedula.getRawValue().length > 0){
		Ext.Ajax.request({
			url: '/SIGP/tutorEmpresarial/buscarTutorEmpresarial',
			method: 'POST',
			params: 'cedula=' + cedula.getValue(),
			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if ((jsonData.success ==true) && (jsonData.errorMsj=='')){
	      					Ext.getCmp('txtCedula').disable();
	      					habilitarCampos(true);
	      				}else if((jsonData.success ==true) && (jsonData.errorMsj!='')){
	      					Ext.Msg.confirm('Registro encontrado.','La c&eacute;dula: '+cedula.getValue()+', ya est&aacute registrada.<BR>&iquest; Quiere actualizar sus campos ?',function(btn){  
         	        			if(btn === 'yes'){
         	        				var datos = jsonData.datos;
	         	        			Ext.getCmp('txtNombre').setValue(datos.nombre);
	         	        			Ext.getCmp('txtApellido').setValue(datos.apellido);
	         	        			Ext.getCmp('txtTelefono').setValue(datos.telefono);
	         	        			Ext.getCmp('txtCorreo').setValue(datos.email);
	         	        			Ext.getCmp('txtCorreoRepetir').setValue(datos.email);
	         	        			Ext.getCmp('txtCargo').setValue(datos.cargo);
	         	        			Ext.getCmp('txtCedula').disable();
	         	        			habilitarCampos(true);
         	        			}else{
         	        				Ext.getCmp('txtCedula').setValue('');
         	        				Ext.getCmp('txtCedula').focus();
	         	        			
    	     	        		}
         	        		 });       				
	      				}
				}
		});
	}
}

});

 function habilitarCampos(flag){
	if (flag==true){
		Ext.getCmp('txtNombre').enable();
		Ext.getCmp('txtApellido').enable();
		Ext.getCmp('txtTelefono').enable();
		Ext.getCmp('txtCorreo').enable();
		Ext.getCmp('txtCorreoRepetir').enable();
     	Ext.getCmp('txtCargo').enable();
	}else{
		Ext.getCmp('txtNombre').disable();
		Ext.getCmp('txtApellido').disable();
		Ext.getCmp('txtTelefono').disable();
		Ext.getCmp('txtCorreo').disable();
		Ext.getCmp('txtCorreoRepetir').disable();
     	Ext.getCmp('txtCargo').disable();
		
	}
	
}