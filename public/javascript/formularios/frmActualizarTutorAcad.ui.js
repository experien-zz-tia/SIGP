/*
 * File: frmActualizarTutorAcad.ui.js
 * Date: Sun Jul 04 2010 22:40:39 GMT-0430 (Hora estándar de Venezuela)
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

frmActualizarTutorAcadUi = Ext.extend(Ext.Window, {
    title: 'Tutor Académico',
    width: 447,
    height: 411,
    layout: 'absolute',
    id: 'frmActualizarTutorAcadWin',
    modal: true,
    resizable: false,
    activeItem: 1,
    
    initComponent: function() {
        this.items = [
            {
                xtype: 'form',
                layout: 'absolute',
                width: 440,
                height: 380,
                
                title: 'Formulario de Registro',
                headerAsText: false,
                unstyled: true,
                method: 'POST',
                waitTitle: 'Por favor espere...',
                url: '/SIGP/tutorAcademico/registrarTutorA',
                fieldLabel: '',
                id: 'registroTutorForm',
                
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Información del Tutor',
                        layout: 'absolute',
                        height: 360,
                        x: -1,
                        y: 10,
                        width: 440,
                        items: [
                            {
                                xtype: 'label',
                                text: 'Cédula*:',
                                x: 5,
                                y: 10,
                                width: 75
                            },
                            {
                                xtype: 'label',
                                text: 'Nombre(s)*:',
                                x: 5,
                                y: 40,
                                width: 75
                            },
                            {
                                xtype: 'label',
                                text: 'Apellido(s)*:',
                                x: 5,
                                y: 70,
                                width: 75
                            },
                            {
                                xtype: 'label',
                                text: 'Decanato*:',
                                x: 5,
                                y: 100,
                                width: 75
                            },
                            {
                                xtype: 'label',
                                text: 'Departamento*:',
                                x: 5,
                                y: 130,
                                width: 75
                            },
                            {
                                xtype: 'label',
                                text: 'Cargo*:',
                                x: 5,
                                y: 160,
                                width: 75
                            },
                            {
                                xtype: 'label',
                                text: 'Correo eletrónico*:',
                                x: 5,
                                y: 190,
                                width: 75
                            },
                            {
                                xtype: 'label',
                                text: 'Repetir correo*:',
                                x: 5,
                                y: 225,
                                width: 95
                            },
                            {
                                xtype: 'label',
                                text: 'Teléfono:',
                                x: 5,
                                y: 250,
                                width: 75
                            },
                            {
                                xtype: 'button',
                                text: 'Guardar',
                                x: 125,
                                y: 290,
                                width: 90,
                                height: 30,
                                iconCls: 'sigp-guardar',
                                id: 'btnGuardar'
                            },
                            {
                                xtype: 'button',
                                text: 'Limpiar',
                                x: 220,
                                y: 290,
                                width: 90,
                                height: 30,
                                iconCls: 'sigp-limpiar',
                                id: 'btnCancelar'
                            },
                            {
                                xtype: 'button',
                                text: 'Salir',
                                x: 315,
                                y: 290,
                                width: 90,
                                height: 30,
                                iconCls: 'sigp-salir',
                                id: 'btnSalir'
                            },
                            {
                                xtype: 'textfield',
                                x: 105,
                                y: 10,
                                width: 145,
                                enableKeyEvents: true,
                                name: 'txtCedula',
                                id: 'txtCedula',
                                listeners:{
                            	specialKey: function(field, el)
                            	{
                            	if (el.getKey()==Ext.EventObject.ENTER || el.getKey()==Ext.EventObject.TAB)
                            		{
                            		Ext.getCmp('txtCedula').fireEvent('submit');
                            		}
                            	}
                            }
                            },
                            {
                                xtype: 'textfield',
                                x: 105,
                                y: 40,
                                width: 255,
                                name: 'txtNombre',
                                id: 'txtNombre'
                            },
                            {
                                xtype: 'textfield',
                                x: 105,
                                y: 70,
                                width: 255,
                                name: 'txtApellido',
                                id: 'txtApellido'
                            },
                            {
                                xtype: 'combo',
                                x: 105,
                                y: 100,
                                width: 305,
                                name: 'cmbDecanato',
                                id: 'cmbDecanato',
                                store: 'stDecanato',
                                editable: false,
                                displayField: 'nombre',
                                valueField: 'id',
                                emptyText: '-Seleccione-',
                                triggerAction: 'all',
                                allowBlank: false,
                                forceSelection: true,
                                loadingText: 'Cargando...',
                                blankText: 'Seleccione un Departamento'

                            },
                            {
                                xtype: 'combo',
                                x: 105,
                                y: 130,
                                width: 305,
                                name: 'cmbDepartamento',
                                id: 'cmbDepartamento',
                                store: 'stDepartamento',
                                editable: false,
                                displayField: 'descripcion',
                                valueField: 'id',
                                emptyText: '-Seleccione-',
                                triggerAction: 'all',
                                queryParam: 'decanato_id',
                                allowBlank: false,
                                forceSelection: true,
                                loadingText: 'Cargando...',
                                mode: 'local',
                                submitValue: false,
                                blankText: 'Seleccione un Departamento'
                            },
                            {
                                xtype: 'textfield',
                                x: 105,
                                y: 160,
                                width: 305,
                                name: 'txtCargo',
                                id: 'txtCargo'
                            },
                            {
                                xtype: 'textfield',
                                x: 105,
                                y: 190,
                                width: 305,
                                name: 'txtCorreo',
                                id: 'txtCorreo'
                            },
                            {
                                xtype: 'textfield',
                                x: 105,
                                y: 220,
                                width: 305,
                                name: 'txtRepetirCorreo',
                                id: 'txtRepetirCorreo'
                            },
                            {
                                xtype: 'textfield',
                                x: 105,
                                y: 250,
                                width: 145,
                                name: 'txtTelefono',
                                id: 'txtTelefono'
                            }
                        ]
                    }
                ]
            }
        ];
        frmActualizarTutorAcadUi.superclass.initComponent.call(this);
    }
});