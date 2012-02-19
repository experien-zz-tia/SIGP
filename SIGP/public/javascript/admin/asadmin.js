Ext.BLANK_IMAGE_URL = $Kumbia.path+"css/ext/resources/images/default/s.gif";
/**
 * @class Ext.grid.TableGrid
 * @extends Ext.grid.Grid
 * A Grid which creates itself from an existing HTML table element.
 * @constructor
 * @param {String/HTMLElement/Ext.Element} table The table element from which this grid will be created -
 * The table MUST have some type of size defined for the grid to fill. The container will be
 * automatically set to position relative if it isn't already.
 * @param {Object} config A config object that sets properties on this grid and has two additional (optional)
 * properties: fields and columns which allow for customizing data fields and columns for this grid.
 * @history
 * 2007-03-01 Original version by Nige "Animal" White
 * 2007-03-10 jvs Slightly refactored to reuse existing classes
 */
Ext.grid.TableGrid = function(table, config) {
  config = config || {};
  Ext.apply(this, config);
  var cf = config.fields || [], ch = config.columns || [];
  table = Ext.get(table);

  var ct = table.insertSibling();

  var fields = [], cols = [];
  var headers = table.query("thead th");
  for (var i = 0, h; h = headers[i]; i++) {
    var text = h.innerHTML;
    var name = 'tcol-'+i;

    fields.push(Ext.applyIf(cf[i] || {}, {
      name: name,
      mapping: 'td:nth('+(i+1)+')/@innerHTML'
    }));

    cols.push(Ext.applyIf(ch[i] || {}, {
      'header': text,
      'dataIndex': name,
      'width': h.offsetWidth,
      'align': h.align,
      'tooltip': h.title,
      'sortable': true
    }));
  }

  var ds  = new Ext.data.Store({
    reader: new Ext.data.XmlReader({
      record:'tbody tr'
    }, fields)
  });

  ds.loadData(table.dom);

  var cm = new Ext.grid.ColumnModel(cols);

  if (config.width || config.height) {
    ct.setSize(config.width || 'auto', config.height || 'auto');
  } else {
    ct.setWidth(table.getWidth());
  }

  if (config.remove !== false) {
    table.remove();
  }

  Ext.applyIf(this, {
    'ds': ds,
    'cm': cm,
    'sm': new Ext.grid.RowSelectionModel(),
    autoHeight: true,
    autoWidth: false
  });
  Ext.grid.TableGrid.superclass.constructor.call(this, ct, {});
};

Ext.extend(Ext.grid.TableGrid, Ext.grid.GridPanel);

var InstanceAdmin = {

	loginWindow: "",
	treePanel: "",

	startSession: function(){
		if($F("login")==""){
			$("login").activate();
			//alert("Debe indicar el login");
			Ext.Msg.show({
   				title:'Advertencia',
   				msg: 'Debe indicar su login',
   				buttons: Ext.Msg.OK,
   				icon: Ext.MessageBox.WARNING
			});
			return false;
		}
		new Ajax.Request(Utils.getKumbiaURL()+"asadmin/startSession", {
			parameters: {
				login: $F("login"),
				password: $F("password")
			},
			onSuccess: function(transport){
				var successLogin = transport.responseText.evalJSON();
				if(successLogin==true){
					InstanceAdmin.loginWindow.hide();
				} else {
					Ext.Msg.show({
   						title:'Error',
   						msg: 'Usuario/Password invalidos',
   						buttons: Ext.Msg.OK,
   						icon: Ext.MessageBox.ERROR
					});
					$("login").activate();
				}
			}
		})
	}

}

Ext.ux.TabCloseMenu = function(){
    var tabs, menu, ctxItem;
    this.init = function(tp){
        tabs = tp;
        tabs.on('contextmenu', onContextMenu);
    }

    function onContextMenu(ts, item, e){
        if(!menu){ // create context menu on first right click
            menu = new Ext.menu.Menu([{
                id: tabs.id + '-close',
                text: 'Cerrar Pesta&ntilde;a',
                handler : function(){
                    tabs.remove(ctxItem);
                }
            },{
                id: tabs.id + '-close-others',
                text: 'Cerrar Todas',
                handler : function(){
                    tabs.items.each(function(item){
                        if(item.closable && item != ctxItem){
                            tabs.remove(item);
                        }
                    });
                }
            }]);
        }
        ctxItem = item;
        var items = menu.items;
        items.get(tabs.id + '-close').setDisabled(!item.closable);
        var disableOthers = true;
        tabs.items.each(function(){
            if(this != item && this.closable){
                disableOthers = false;
                return false;
            }
        });
        items.get(tabs.id + '-close-others').setDisabled(disableOthers);
        menu.showAt(e.getPoint());
    }
};

Ext.ux.ApplicationActions = function(){

    var treePanel, menu;

    this.init = function(tp){
        treePanel = tp;
        treePanel.on('contextmenu', onContextMenu);
    }

    function onContextMenu(node, e){
        if(!menu){
            menu = new Ext.menu.Menu([{
                id: treePanel.id + '-model',
                text: 'Crear Modelo',
                handler : function(){

                }
            },{
                id: treePanel.id + '-plugin',
                text: 'Crear Plugin',
                handler : function(){

                }
            }]);
        }
        menu.showAt(e.getPoint());
    }
};

Ext.onReady(function(){

	InstanceAdmin.loginWindow = new Ext.Window({
		layout      : 'fit',
		width       : 340,
		height      : 160,
		closable	: false,
		closeAction :'hide',
		plain       : true,
		modal		: true,
		title		: "Kumbia Enterprise  Instance Admin",
		items       : new Ext.form.FieldSet({
                title: 'Datos de Sesi&oacute;n',
                autoHeight: true,
                defaultType: 'textfield',
                items: [{
                		id: 'login',
                        fieldLabel: 'Login',
                        name: 'login',
                        width: 190,
                        value: 'admin'
                    },{
                        fieldLabel: 'Password',
                        inputType: 'password',
                        id: 'password',
                        name: 'password',
                        width: 190,
                        value: "admin"
                    }
				]
		}),
		buttons: [{
			text	: 'Iniciar Sesi&oacute;n',
			handler	: InstanceAdmin.startSession
		}]
    });

	var tabs = new Ext.TabPanel({
        resizeTabs: true,
        minTabWidth: 115,
        tabWidth: 170,
        enableTabScroll: true,
        width: 600,
        height: 250,
        defaults: {
        	autoScroll:true,
			bodyStyle: 'padding:15px'
        },
        plugins: new Ext.ux.TabCloseMenu()
    });

	var initTab = tabs.add({
        title: "Inicio",
		contentEl: 'start-div',
		closable: true
	});

	tabs.setActiveTab(initTab);
	Ext.QuickTips.init();
	var detailEl;

	var contentPanel = {
		id: 'content-panel',
		region: 'center',
		layout: 'card',
		margins: '2 5 5 0',
		activeItem: 0,
		border: false,
		items: [tabs]
	};

    InstanceAdmin.treePanel = new Ext.tree.TreePanel({
    	id: 'tree-panel',
    	title: 'Tareas Comunes',
        region:'north',
        split: true,
        height: 300,
        width: 250,
        minSize: 150,
        autoScroll: true,

        rootVisible: false,
        lines: false,
        singleExpand: true,
        useArrows: true,

        loader: new Ext.tree.TreeLoader({
            dataUrl: Utils.getKumbiaURL()+"asadmin/getApplications"
        }),

        root: new Ext.tree.AsyncTreeNode(),
        plugins: new Ext.ux.ApplicationActions()
    });


    InstanceAdmin.treePanel.on('click', function(n){
    	var sn = this.selModel.selNode || {};
    	//alert(n.id)
    	if(n.leaf && n.id != sn.id){
    		if(n.id.substring(0, 3)=="log"){
    			new Ajax.Request(Utils.getKumbiaURL()+"asadmin/getApplicationLog/"+n.id, {
    				onSuccess: function(transport){
    					createdTab = tabs.add({
    						id: "tab"+n.id,
            				title: n.text,
            				iconCls: 'node-logs',
							bodyStyle:'padding:10px;',
            				closable: true,
            				html: transport.responseText
        				});
            			createdTab.show();
            			tabs.setActiveTab(createdTab);
            			if($("table-"+n.text)){
            				var grid = new Ext.grid.TableGrid("table-"+n.text, {
            					width: "100%",
 						    	stripeRows: true,
 						    	sortAscText: "Ordenar Ascendentemente"
    						});
    						grid.render();
            			}
    				}
    			})
    		}
    		if(n.id.substring(0, 4)=="conf"){
				new Ajax.Request(Utils.getKumbiaURL()+"asadmin/getConfiguration/"+n.id, {
					onSuccess: function(transport){
						createdTab = tabs.add({
    						id: "tab"+n.id,
            				title: n.text,
            				iconCls: 'node-conf',
							bodyStyle:'padding:10px;',
            				closable: true,
            				html: transport.responseText
        				});
        				createdTab.show();
            			tabs.setActiveTab(createdTab);

            			$("data-"+n.id).innerHTML.evalJSON().each(function(section){
							propsGrid = new Ext.grid.PropertyGrid({
        						el: 'el-'+n.id+'-'+section.name,
        						title: section.name,
        						nameText: section.name,
        						width: 500,
        						autoHeight: true,
        						viewConfig : {
            						forceFit: true,
            						scrollOffset: 2
        						}
    						});

    						propsGrid.addButton("Guardar", function(){
    							alert("bola")
    						})

    						propsGrid.render();

    						propsGrid.setSource(section.settings);

            			});
					}
				})
    		}
    	}
    	if(n.id=="node-monitor"){
    		new Ajax.Request(Utils.getKumbiaURL()+"asadmin/getMonitorStatus", {
    			onSuccess: function(transport){
    				createdTab = tabs.add({
    					id: "tab"+n.id,
            			title: "Estado Aplicaciones",
            			iconCls: 'node-monitor',
						bodyStyle:'padding:10px;',
            			closable: true,
            			html: transport.responseText
        			});
            		createdTab.show();
            		tabs.setActiveTab(createdTab);
            		if($("table-monitor")){
            			var grid = new Ext.grid.TableGrid("table-monitor", {
            				width: "100%",
 					    	stripeRows: true,
 					    	sortAscText: "Ordenar Ascendentemente"
    					});
    					grid.render();
            		}
    			}
    		});
    	}
    });

	var detailsPanel = {
		id: 'details-panel',
        title: 'Informaci&oacute;n del Servidor',
        region: 'center',
        bodyStyle: 'padding-bottom:10px;background:#eee;',
		autoScroll: true,
		html: '<p class="details-info">'+$("server-info").innerHTML+'</p>'
    };

    InstanceAdmin.loginWindow.show();
    var viewPort = new Ext.Viewport({
		layout: 'border',
		title: 'Kumbia Enteprise Admin',
		items: [{
			xtype: 'box',
			region: 'north',
			applyTo: 'header',
			height: 30
		},{
			layout: 'border',
	    	id: 'layout-browser',
	        region:'west',
	        border: false,
	        split: true,
			margins: '2 0 5 5',
	        width: 250,
	        minSize: 100,
	        maxSize: 500,
			items: [InstanceAdmin.loginWindow, InstanceAdmin.treePanel, detailsPanel]
		},
			contentPanel
		],
        renderTo: Ext.getBody()
    });

});
