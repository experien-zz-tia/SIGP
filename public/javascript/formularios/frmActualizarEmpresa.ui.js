
frmActualizarEmpresaUi = Ext.extend(Ext.Panel, {
	title : 'Empresa',
	width : 563,
	height : 469,
	layout : 'form',
	id : 'frmActualizarEmpresaWin',
	resizable : false,
	activeItem : 1,
	initComponent : function() {
		this.items = [{
			xtype : 'form',
			title : 'Formulario de registro',
			headerAsText : false,
			unstyled : true,
			method : 'POST',
			waitTitle : 'Por favor espere...',
			url : '/SIGP/registro/registrarEmpresa',
			id : 'actualizarEmpresaForm',
			items : [{
				xtype : 'tabpanel',
				activeTab : 0,
				enableTabScroll : true,
				items : [{
					xtype : 'panel',
					title : 'Básico',
					width : 571,
					height : 367,
					items : [{
						xtype : 'fieldset',
						title : 'Información básica',
						autoHeight : true,
						width : 545,
						items : [{
							xtype : 'textfield',
							name : 'txtRazonSocial',
							fieldLabel : 'Razón social*',
							anchor : '100%',
							maxLength : 45,
							allowBlank : false,
							id : 'txtRazonSocial'
						}, {
							xtype : 'textfield',
							name : 'txtRif',
							editable : false,
							width : 320,
							maxLength : 12,
							anchor : '100%',
							fieldLabel : 'R.I.F.*',
							allowBlank : false,
							id : 'txtRif'
						}, {
							xtype : 'textarea',
							name : 'txtDireccion',
							width : 284,
							anchor : '100%',
							fieldLabel : 'Dirección*',
							maxLength : 45,
							allowBlank : false,
							id : 'txtDireccion'
						}, {
							xtype : 'combo',
							name : 'cmbEstado',
							anchor : '50%',
							fieldLabel : 'Estado*',
							editable : false,
							store : 'stEstado',
							displayField : 'nombre',
							valueField : 'id',
							emptyText : '-Seleccione-',
							triggerAction : 'all',
							allowBlank : false,
							forceSelection : true,
							loadingText : 'Cargando...',
							blankText : 'Seleccione un estado.',
							submitValue : false,
							id : 'cmbEstado'
						}, {
							xtype : 'combo',
							name : 'cmbCiudad',
							anchor : '50%',
							fieldLabel : 'Ciudad*',
							editable : false,
							store : 'stCiudad',
							displayField : 'nombre',
							valueField : 'id',
							triggerAction : 'all',
							queryParam : 'idEstado',
							allowBlank : false,
							loadingText : 'Cargando...',
							forceSelection : true,
							emptyText : '-Seleccione-',
							blankText : 'Seleccione una ciudad.',
							mode : 'local',
							submitValue : false,
							id : 'cmbCiudad'
						}, {
							xtype : 'textfield',
							fieldLabel : 'Teléfono*',
							name : 'txtTelefono',
							anchor : '50%',
							allowBlank : false,
							maxLength : 12,
							vtype : 'soloNumero',
							id : 'txtTelefono'
						}, {
							xtype : 'textfield',
							name : 'txtTelefono2',
							fieldLabel : 'Teléfono secundario',
							anchor : '50%',
							maxLength : 12,
							vtype : 'soloNumero',
							id : 'txtTelefono2'
						}, {
							xtype : 'textarea',
							anchor : '100%',
							fieldLabel : 'Descripción*',
							maxLength : 140,
							allowBlank : false,
							id : 'txtDescripcion'
						}, {
							xtype : 'textfield',
							fieldLabel : 'Sitio web',
							anchor : '100%',
							name : 'txtWeb',
							maxLength : 45,
							vtype : 'url',
							id : 'txtWeb'
						},
                        {
                            xtype: 'textfield',
                            id: 'txtIdEmpresa',
                            hidden: true
                        }]
					}]
				}, {
					xtype : 'panel',
					title : 'Contacto',
					items : [{
						xtype : 'fieldset',
						title : 'Información de contacto',
						items : [{
							xtype : 'textfield',
							fieldLabel : 'Representante*',
							anchor : '100%',
							width : 377,
							name : 'txtRepresentante',
							maxLength : 45,
							allowBlank : false,
							id : 'txtRepresentante'
						}, {
							xtype : 'textfield',
							fieldLabel : 'Cargo*',
							anchor : '100%',
							maxLength : 45,
							name : 'txtCargo',
							allowBlank : false,
							id : 'txtCargo'
						}, {
							xtype : 'textfield',
							fieldLabel : 'Correo electrónico*',
							anchor : '100%',
							name : 'txtCorreo',
							maxLength : 40,
							allowBlank : false,
							vtype : 'email',
							id : 'txtCorreo'
						}, {
							xtype : 'textfield',
							fieldLabel : 'Repetir correo electrónico*',
							anchor : '100%',
							width : 420,
							maxLength : 40,
							name : 'txtCorreoRepetir',
							allowBlank : false,
							vtype : 'emailIguales',
							campoInicial : 'txtCorreo',
							id : 'txtCorreoRepetir'
						}]
					}]
				}]
			}]
		}, {
			xtype : 'container',
			flex : 1,
			layout : 'absolute',
			width : 552,
			height : 30,
			items : [{
				xtype : 'button',
				text : 'Actualizar',
				flex : 1,
				x : 360,
				y : 0,
				width: 90,
                height: 30,
				type : 'submit',
				iconCls : 'sigp-guardar',
				ref : '../btnRegistrar',
				id : 'btnRegistrar'
			}, {
				xtype : 'button',
				text : 'Limpiar',
				flex : 1,
				x : 450,
				y : 0,
				width: 90,
                height: 30,
				type : 'reset',
				iconCls : 'sigp-limpiar',
				ref : '../btnCancelar',
				id : 'btnCancelar'
			}]
		}, {
			xtype : 'label',
			text : 'Campos marcados con * son obligatorios.'
		}];
		frmActualizarEmpresaUi.superclass.initComponent.call(this);
	}
});
