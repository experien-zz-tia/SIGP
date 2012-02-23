
frmConfirmacionTutorUi  = Ext.extend(Ext.Panel, {
    width: 560,
    height: 200,
    title: 'Confirmaci&oacute;n',
    id: 'panelConfirmacionTutor',
    initComponent: function() {
        this.items = [
            {
                xtype: 'form',
                title: 'Formulario de confirmaci&oacute;n',
                headerAsText: false,
                unstyled: true,
                method: 'POST',
                waitTitle: 'Por favor espere...',
                url: '/SIGP/tutorEmpresarial/confirmar',
                id: 'confirmacionTutorForm',
                items: [{
                           xtype: 'fieldset',
                                        title: 'Información de acceso',
                                        height: 109,
                                        width: 545,
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Usuario*',
                                                anchor: '100%',
                                                name: 'txtUsuario',
                                                maxLength: 20,
                                                allowBlank: false,
                                                minLength: 6,
                                                id: 'txtUsuario'
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Clave*',
                                                anchor: '100%',
                                                name: 'txtClave',
                                                inputType: 'password',
                                                maxLength: 20,
                                                allowBlank: false,
                                                minLength: 6,
                                                submitValue: false,
                                                id: 'txtClave'
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Reingresar clave*',
                                                anchor: '100%',
                                                name: 'txtClave2',
                                                inputType: 'password',
                                                maxLength: 20,
                                                allowBlank: false,
                                                minLength: 6,
                                                vtype: 'password',
                                                campoInicialClave:'txtClave',
                                                submitValue: false,
                                                id: 'txtClave2'
                                            },
				                            {
				                                xtype: 'textfield',
				                                id: 'txtHash',
				                                hidden: true
				                            }
                                        ]
                          }
                        ]
            },
            {
                xtype: 'container',
                flex: 1,
                layout: 'absolute',
                width: 552,
                height: 30,
                items: [
                    {
                        xtype: 'button',
                        text: 'Registrar',
                        flex: 1,
                        x: 360,
                        y: 0,
                        width: 90,
                        height: 30,
                        type: 'submit',
                        iconCls: 'sigp-guardar',
                        id: 'btnRegistrar'
                    },
                    {
                        xtype: 'button',
                        text: 'Limpiar',
                        flex: 1,
                        x: 450,
                        y: 0,
                        width: 90,
                        height: 30,
                        iconCls: 'sigp-limpiar',
                        id: 'btnLimpiar'
                    }
                ]
            },
            {
                xtype: 'label',
                text: 'Campos marcados con * son obligatorios.'
            }
        ];
        frmConfirmacionTutorUi.superclass.initComponent.call(this);
    }
});
