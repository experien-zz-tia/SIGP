/*
 * Date: Thu Mar 24 2011 12:55:25 GMT-0430 (Hora estándar de Venezuela)
 */

frmConfirmacionTutorAcadUi  = Ext.extend(Ext.Panel, {
    width: 560,
    height: 200,
    title: 'Confirmaci&oacute;n',
    id: 'frmConfirmacionTAcadWin',
    initComponent: function() {
        this.items = [
            {
                xtype: 'form',
                title: 'Formulario de confirmaci&oacute;n',
                headerAsText: false,
                unstyled: true,
                method: 'POST',
                waitTitle: 'Por favor espere...',
                url: '/SIGP/tutorAcademico/confirmar',
                id: 'confirmacionTutorAcadForm',
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
                        iconCls : 'sigp-guardar',
                        type: 'submit',
                        ref: '../btnRegistrar',
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
                        type: 'reset',
                        iconCls : 'sigp-limpiar',
                        ref: '../btnCancelar',
                        id: 'btnCancelar'
                    }
                ]
            },
            {
                xtype: 'label',
                text: 'Campos marcados con * son obligatorios.'
            }
        ];
        frmConfirmacionTutorAcadUi.superclass.initComponent.call(this);
    }
});
