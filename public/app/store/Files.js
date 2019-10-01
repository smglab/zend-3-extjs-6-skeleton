Ext.define('TestProj.store.Files', {
    extend: 'Ext.data.Store',
    model: 'TestProj.model.File',
    pageSize: 10,
    alias: 'store.archive',
    remoteSort: true,
    simpleSortMode: true,
    autoSync: true,

    proxy: {
        type: 'rest',
        url: '/archive',
        reader: {
            type: 'json',
            rootProperty: 'items',
            totalProperty: 'total'
        },
        writer: {
            type: 'json'
        }
    },
    autoLoad: true,
});
