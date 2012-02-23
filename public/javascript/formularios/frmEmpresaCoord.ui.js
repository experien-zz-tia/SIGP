frmEmpresaCoordUi = Ext.extend(Ext.Window, {
    title: 'Empresa',
    width: 563,
    height: 469,
    layout: 'form',
    id: 'frmEmpresaWin',
    modal: true,
    resizable : false,
    activeItem: 1,
    initComponent: function() {
        this.items = [
            {
                xtype: 'form',
                title: 'Formulario de registro',
                headerAsText: false,
                unstyled: true,
                method: 'POST',
                waitTitle: 'Por favor espere...',
                url: '/SIGP/registro/registrarEmpresa',
                id: 'registroEmpresaForm',
                items: [
                    {
                        xtype: 'tabpanel',
                        activeTab: 0,
                        enableTabScroll: true,
                        items: [
                            {
                                xtype: 'panel',
                                title: 'B&aacute;sico',
                                width: 571,
                                height: 367,
                                items: [
                                    {
                                        xtype: 'fieldset',
                                        title: 'Informaci&oacute;n b&aacute;sica',
                                        autoHeight: true,
                                        width: 545,
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                name: 'txtRazonSocial',
                                                fieldLabel: 'Raz&oacute;n social*',
                                                anchor: '100%',
                                                maxLength: 45,
                                                allowBlank: false,
                                                id: 'txtRazonSocial'
                                            },
                                            {
                                                xtype: 'textfield',
                                                name: 'txtRif',
                                                width: 320,
                                                maxLength: 12,
                                                anchor: '100%',
                                                fieldLabel: 'R.I.F.*',
                                                allowBlank: false,
                                                id: 'txtRif'
                                            },
                                            {
                                                xtype: 'textarea',
                                                name: 'txtDireccion',
                                                width: 284,
                                                anchor: '100%',
                                                fieldLabel: 'Direcci&oacute;n*',
                                                maxLength: 45,
                                                allowBlank: false,
                                                id: 'txtDireccion'
                                            },
                                            {
                                                xtype: 'combo',
                                                name: 'cmbEstado',
                                                anchor: '50%',
                                                fieldLabel: 'Estado*',
                                                editable: false,
                                                store: 'stEstado',
                                                displayField: 'nombre',
                                                valueField: 'id',
                                                emptyText: '-Seleccione-',
                                                triggerAction: 'all',
                                                allowBlank: false,
                                                forceSelection: true,
                                                loadingText: 'Cargando...',
                                                blankText: 'Seleccione un estado.',
                                                submitValue: false,
                                                id: 'cmbEstado'
                                            },
                                            {
                                                xtype: 'combo',
                                                name: 'cmbCiudad',
                                                anchor: '50%',
                                                fieldLabel: 'Ciudad*',
                                                editable: false,
                                                store: 'stCiudad',
                                                displayField: 'nombre',
                                                valueField: 'id',
                                                triggerAction: 'all',
                                                queryParam: 'idEstado',
                                                allowBlank: false,
                                                loadingText: 'Cargando...',
                                                forceSelection: true,
                                                emptyText: '-Seleccione-',
                                                blankText: 'Seleccione una ciudad.',
                                                mode: 'local',
                                                submitValue: false,
                                                id: 'cmbCiudad'
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Tel&eacute;fono*',
                                                name: 'txtTelefono',
                                                anchor: '50%',
                                                allowBlank: false,
                                                maxLength: 12,
                                                vtype: 'soloNumero',
                                                id: 'txtTelefono'
                                            },
                                            {
                                                xtype: 'textfield',
                                                name: 'txtTelefono2',
                                                fieldLabel: 'Tel&eacute;fono secundario',
                                                anchor: '50%',
                                                maxLength: 12,
                                                vtype: 'soloNumero',
                                                id: 'txtTelefono2'
                                            },
                                            {
                                                xtype: 'textarea',
                                                anchor: '100%',
                                                fieldLabel: 'Descripci&oacute;n*',
                                                maxLength: 140,
                                                allowBlank: false,
                                                id: 'txtDescripcion'
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Sitio web',
                                                anchor: '100%',
                                                name: 'txtWeb',
                                                maxLength: 45,
                                                vtype: 'url',
                                                id: 'txtWeb'
                                            }
                                            ,
				                            {
				                                xtype: 'textfield',
				                                id: 'txtIdEmpresa',
				                                hidden: true
				                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                xtype: 'panel',
                                title: 'Contacto',
                                items: [
                                    {
                                        xtype: 'fieldset',
                                        title: 'Informaci&oacute;n de contacto',
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Representante*',
                                                anchor: '100%',
                                                width: 377,
                                                name: 'txtRepresentante',
                                                maxLength: 45,
                                                allowBlank: false,
                                                id: 'txtRepresentante'
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Cargo*',
                                                anchor: '100%',
                                                maxLength: 45,
                                                name: 'txtCargo',
                                                allowBlank: false,
                                                id: 'txtCargo'
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Correo electr&oacute;nico*',
                                                anchor: '100%',
                                                name: 'txtCorreo',
                                                maxLength: 40,
                                                allowBlank: false,
                                                vtype: 'email',
                                                id: 'txtCorreo'
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Repetir correo electr&oacute;nico*',
                                                anchor: '100%',
                                                width: 420,
                                                maxLength: 40,
                                                name: 'txtCorreoRepetir',
                                                allowBlank: false,
                                                vtype: 'emailIguales',
                                                campoInicial: 'txtCorreo',
                                                id: 'txtCorreoRepetir'
                                            }
                                        ]
                                    }
                                ]
                            }
                            
                        ]
                    }
                ]
            },
           {
                xtype: 'container',
                layout: 'hbox',
                id: 'contenedorBtns',
                layoutConfig: {
                    pack: 'end'
                },
                items: [{
                        xtype: 'button',
                        text: 'Registrar',
                        type: 'submit',
                        width: 90,
                        height: 30,
                        iconCls: 'sigp-guardar',
                        id: 'btnRegistrar'
                    },
             		{
                        xtype: 'button',
                        text: 'Actualizar',
                        width: 90,
                        height: 30,
                        iconCls: 'sigp-publicar',
                        hidden: true,
                        id: 'btnActualizar'
                   },
                    {
                        xtype: 'button',
                        text: 'Limpiar',
                        width: 90,
                        height: 30,
                         iconCls: 'sigp-limpiar',
                        id: 'btnLimpiar'
                    },
                    {
                        xtype: 'button',
                        text: 'Salir',
                        width: 90,
                        height: 30,
                        iconCls: 'sigp-salir',
                        id: 'btnSalir'
                    }
                ]
            },
            {
                xtype: 'label',
                text: 'Campos marcados con * son obligatorios.'
            }
        ];
        frmEmpresaCoordUi.superclass.initComponent.call(this);
    }
});
