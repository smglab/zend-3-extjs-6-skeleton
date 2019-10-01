Ext.define('TestProj.form.field.ZipFiles', {
    extend: 'Ext.form.FieldContainer',
    mixins: {
        field: 'Ext.form.field.Field'
    },
    alias: 'zip-files',
    xtype: 'zip-files',
    layout: 'vbox',
    combineErrors: true,
    _filesValue: [],

    initComponent: function () {

        this.items = [
            {
                xtype: 'fieldcontainer',
                items: []
            }
        ];
        this.callParent(arguments);
    },

    getValue: function () {
        return this._filesValue;
    },

    setValue: function (value) {
        this.removeAll();
        value.forEach((item, i) => {
            this.add(this.createItem(item, i));
        });
    },

    getSubmitData: function () {
        var result = {};
        result[this.getName()] = this._filesValue.map(file => file.name).join(',');
        return result;
    },
    createItem: function (record, i) {
        var cmp = this;
        var wrapWidth = this.width - 45;
        return {
            xtype: 'fieldcontainer',
            layout: 'hbox',
            height: 18,
            items: [{
                    html: `${record.name}`,
                    id: 'txt-' + i,
                    width: wrapWidth
                }, {
                    iconCls: 'x-fa fa-times',
                    xtype: 'button',
                    background: null,
                    width: 10,
                    height: 10,
                    ui: 'round',
                    margin: '0 5px',
                    handler: function () {
                        var text = Ext.getCmp('txt-' + i);
                        isToRemove = !cmp._filesValue.includes(record);
                        var newValue = `${record.name}`;
                        if(isToRemove) {
                            newValue = `<strike>${record.name}</strike>`
                            cmp._filesValue.push(record);
                        } else {
                            var index = cmp._filesValue.indexOf(record);
                            cmp._filesValue.splice(index, 1);
                        }
                        text.update(newValue);
                    }
            }]
        }

    }
});
