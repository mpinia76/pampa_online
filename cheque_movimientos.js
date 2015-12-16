Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.ux', 'library/extjs/ux');
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.ux.grid.FiltersFeature',
    'Ext.toolbar.Paging'
]);

Ext.onReady(function(){

    Ext.QuickTips.init();

    // configure whether filter query is encoded or not (initially)
    var encode = false;
    
    // configure whether filtering is performed locally or remotely (initially)
    var local = true;

    var store = Ext.create('Ext.data.JsonStore', {
        // store configs
        autoDestroy: true,

        proxy: {
            type: 'ajax',
            url: 'cheques_movimientos.json.php',
            reader: {
                type: 'json',
                root: 'data',
                idProperty: 'id',
                totalProperty: 'total'
            }
        },

        remoteSort: false,
        sortInfo: {
            field: 'fecha',
            direction: 'DESC'
        },
        pageSize: 10,
        storeId: 'cheques',
        fields: [
            { name: 'id', type: 'int' },
            { name: 'fecha',  type: 'date', dateFormat: 'Y-m-d' },
			{ name: 'debitado', type: 'date', dateFormat: 'Y-m-d' },
			{ name: 'mes' },
			{ name: 'banco' },
			{ name: 'nombre' },
			{ name: 'numero' },
			{ name: 'titular' },
			{ name: 'concepto' },
            { name: 'monto', type: 'float' },
            { name: 'estado' }
        ]
    });

    var createHeaders = function (finish, start) {

        var columns = [{
            dataIndex: 'id',
            text: 'Id',
            filterable: true,
            width: 30,
            filter: {
				type: 'numeric'
			}
        }, {
            dataIndex: 'fecha',
            text: 'Fecha de pago',
            filterable: true,
			renderer: Ext.util.Format.dateRenderer('d/m/Y'),
            width: 60,
            filter: {
				type: 'date'
			}
        }, {
            dataIndex: 'fecha',
            text: 'Fecha debitado',
            filterable: true,
			renderer: Ext.util.Format.dateRenderer('d/m/Y'),
            width: 60,
            filter: {
				type: 'date'
			}
        }, {
            dataIndex: 'mes',
            text: 'Mes',
			width: 40,
			filterable: true,
            filter: {
                type: 'list',
                options: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
            }
        }, {
            dataIndex: 'banco',
            text: 'Banco',
			width: 80,
			filterable: true
        }, {
            dataIndex: 'nombre',
            text: 'Cuenta',
			width: 80,
			filterable: true
        }, {
            dataIndex: 'numero',
            text: 'Numero',
			width: 80,
			filterable: true
        }, {
            dataIndex: 'titular',
            text: 'Titular',
			width: 80,
			filterable: true
        }, {
            dataIndex: 'concepto',
            text: 'Concepto',
			width: 80,
			filterable: true
        }, {
            dataIndex: 'monto',
            text: 'Monto',
			width: 40,
			filterable: true,
			filter: {
				type: 'float'
			}
        }, {
            dataIndex: 'estado',
            text: 'Estado',
			width: 60,
			filterable: true,
            filter: {
                type: 'list',
                options: ['Pendiente', 'Debitado']
            }
        }];

        return columns.slice(start || 0, finish);
    };
	
    var filters = {
        ftype: 'filters',
        // encode and local configuration options defined previously for easier reuse
        encode: encode, // json encode the filter query
        local: local,   // defaults to false (remote filtering)
        filters: [{
            type: 'list',
            dataIndex: 'estado',
            options: ['Pendiente', 'Debitado']
        }]
    };
	
    var grid = Ext.create('Ext.grid.Panel', {
        border: false,
        store: store,
		renderTo: 'grid-example',
		height: 320,
        columns: createHeaders(11),
        loadMask: true,
        features: [filters],
        bbar: Ext.create('Ext.toolbar.Paging', {
            store: store
        })
    });

    // add some buttons to bottom toolbar just for demonstration purposes
    grid.child('[dock=bottom]').add([
        '->',
        {
            text: 'Encode: ' + (encode ? 'On' : 'Off'),
            tooltip: 'Toggle Filter encoding on/off',
            enableToggle: true,
            handler: function (button, state) {
                var encode = (grid.filters.encode !== true);
                var text = 'Encode: ' + (encode ? 'On' : 'Off'); 
                grid.filters.encode = encode;
                grid.filters.reload();
                button.setText(text);
            } 
        },
        {
            text: 'Local Filtering: ' + (local ? 'On' : 'Off'),
            tooltip: 'Toggle Filtering between remote/local',
            enableToggle: true,
            handler: function (button, state) {
                var local = (grid.filters.local !== true),
                    text = 'Local Filtering: ' + (local ? 'On' : 'Off'),
                    newUrl = local ? url.local : url.remote,
                    store = grid.view.getStore();
                 
                // update the GridFilter setting
                grid.filters.local = local;
                // bind the store again so GridFilters is listening to appropriate store event
                grid.filters.bindStore(store);
                // update the url for the proxy
                store.proxy.url = newUrl;

                button.setText(text);
                store.load();
            } 
        },
        {
            text: 'All Filter Data',
            tooltip: 'Get Filter Data for Grid',
            handler: function () {
                var data = Ext.encode(grid.filters.getFilterData());
                Ext.Msg.alert('All Filter Data',data);
            } 
        },{
            text: 'Clear Filter Data',
            handler: function () {
                grid.filters.clearFilters();
            } 
        },{
            text: 'Add Columns',
            handler: function () {
                if (grid.headerCt.items.length < 6) {
                    grid.headerCt.add(createHeaders(6, 4));
                    grid.view.refresh();
                    this.disable();
                }
            }
        }    
    ]);


    store.load();
});