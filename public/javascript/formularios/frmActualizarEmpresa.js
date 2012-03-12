
var sw= false;

Ext.apply(Ext.form.VTypes, {
	soloLetrasNumerosMask: /[a-zA-Z0-9]/,
	soloLetrasNumeros: function(value,field){  
    return true ;
},
soloLetrasNumerosText: 'Solo debe contener A-Z,a-z,0-9 .<br/>Por favor, verifiquelas.'
});

Ext.apply(Ext.form.VTypes, {
	soloNumeroMask: /[ \d\-\(\)]/,
	soloNumero: function(value,field){  
    return value.replace(/[ \-\(\)]/g,'').length == 12 ;
}

});

Ext.apply(Ext.form.VTypes, {
	password: function(val, field) {
	if (field.campoInicialClave) {
		var pwd = Ext.getCmp(field.campoInicialClave);
		return (val == pwd.getValue());
	}
	
	return true;
},
passwordText: 'Contrase\u00f1as no coinciden.<br/>Por favor, verifiquelas.'
});

Ext.apply(Ext.form.VTypes, {
	emailIguales: function(val, field) {
	if (field.campoInicial) {
		var email = Ext.getCmp(field.campoInicial);
		if  (val != email.getValue()){
			field.markInvalid(); 
			return false;
		}
	}
	return true;
},
emailIgualesText:'Cuentas de correo no coindicen.<Br/>Por favor, verifiquelas.'
});

Ext.QuickTips.init(); 
frmActualizarEmpresa = Ext.extend(frmActualizarEmpresaUi, {
    initComponent: function() {
	frmActualizarEmpresa.superclass.initComponent.call(this);
		Ext.getCmp('cmbEstado').on('select',this.actualizarCiudades);
		Ext.getCmp('btnRegistrar').on('click',this.registrarEmpresa);
		Ext.getCmp('btnCancelar').on('click',this.cancelar);
		this.buscarEmpresa();
	    },
	actualizarCiudades:function(){
	  Ext.getCmp('cmbCiudad').clearValue();
	  Ext.getCmp('cmbCiudad').store.reload({params: {idEstado: Ext.getCmp('cmbEstado').getValue()}});
	},
	registrarEmpresa:function(){
		// Se verifica que los campos marcados como obligatorios
		// (allowBlank:false) esten llenos
		if (Ext.getCmp('actualizarEmpresaForm').getForm().isValid()){
			 Ext.getCmp('actualizarEmpresaForm').getForm().submit({ 
				 url: '/SIGP/empresa/actualizarEmpresa',
				 waitMsg : 'Enviando datos...', 
				params: {pEmpresaId :Ext.getCmp('txtIdEmpresa').getValue(),
					pRazonSocial :Ext.getCmp('txtRazonSocial').getValue(),
					pDireccion :Ext.getCmp('txtDireccion').getValue(),
					pCiudad :Ext.getCmp('cmbCiudad').getValue(),
					pEstado :Ext.getCmp('cmbEstado').getValue(),
					pTelefono :Ext.getCmp('txtTelefono').getValue(),
					pTelefono2 :Ext.getCmp('txtTelefono2').getValue(),
					pDescripcion :Ext.getCmp('txtDescripcion').getValue(),
					pWeb :Ext.getCmp('txtWeb').getValue(),
					pRepresentante :Ext.getCmp('txtRepresentante').getValue(),
					pCargo :Ext.getCmp('txtCargo').getValue(),
					pCorreo :Ext.getCmp('txtCorreo').getValue()
   				},
				      failure: function (form, action){
                       Ext.MessageBox.show({  
			           title: 'Error',  
			           msg: 'Error al actualizar',  
			           buttons: Ext.MessageBox.OK,  
			           icon: Ext.MessageBox.ERROR  
			           });  
			    },  
			       success: function (form, request){   
			         Ext.MessageBox.show({  
			         title: 'Informaci&oacute;n',  
			         msg: 'Actualizaci&oacute;n exitosa',  
			         buttons: Ext.MessageBox.OK,  
			         icon: Ext.MessageBox.INFO
			         });
			         }
			    }); 
		} else {
		   Ext.MessageBox.show({
		     title: "Error",
		     msg: "Datos incompletos o no v&aacute;lidos, por favor verifique.",
		     width:400,
		     buttons: Ext.MessageBox.OK,
		     icon: Ext.MessageBox.ERROR
		    });
	}},
	cancelar:function(){
		 Ext.getCmp('actualizarEmpresaForm').getForm().reset();
	},

	buscarEmpresa:function(){

  	  Ext.Ajax.request({
			url: '/SIGP/registro/empresaRegistrada',
			method: 'POST',
			
			success: function(respuesta, request) {
				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
				if (jsonData.success == true){
					var resultado= jsonData.resultado;
					// Mostramos los valores obtenidos
					Ext.getCmp('txtRif').setValue(resultado.rif);
					Ext.getCmp('txtRazonSocial').setValue(resultado.razonSocial);
					Ext.getCmp('txtTelefono').setValue(resultado.telefono);
					Ext.getCmp('txtTelefono2').setValue(resultado.telefono2);
					Ext.getCmp('txtDescripcion').setValue(resultado.descripcion);
					Ext.getCmp('txtDireccion').setValue(resultado.direccion);
					Ext.getCmp('txtRepresentante').setValue(resultado.representante);
					Ext.getCmp('txtCargo').setValue(resultado.cargo);
					Ext.getCmp('txtCorreo').setValue(resultado.correo);
					Ext.getCmp('txtCorreoRepetir').setValue(resultado.correo);
					Ext.getCmp('txtWeb').setValue(resultado.web);
					var comboEst = Ext.getCmp('cmbEstado');      					
					var storeEst = comboEst.getStore();
					storeEst.load({
					   callback: function() {
					      comboEst.setValue(resultado.estadoId);
					   }
					});
					var comboC = Ext.getCmp('cmbCiudad');      					
					var store = comboC.getStore();
					store.load({
					   params: {idEstado: resultado.estadoId},
					   callback: function() {
					      comboC.setValue(resultado.ciudadId);
					   }
					});
					Ext.getCmp('txtIdEmpresa').setValue(resultado.id);
				}else{
					Ext.Msg.alert('Operaci&oacute;n no completada','No se han obtenido los datos.');
				}         				
			},
			failure: function ( respuesta, request) {
				Ext.MessageBox.show({
       		     title: "Operaci&oacute;n no realizada.",
       		     msg: "No se pueden obtener los datos. Intente de nuevo.",
       		     width:400,
       		     buttons: Ext.MessageBox.OK,
       		     icon: Ext.MessageBox.ERROR
       		    });
			}
		});
	}

});
