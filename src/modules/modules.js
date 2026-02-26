import { dom } from 'js/dom';
import { Plugin_Manager } from 'src/manager/manager';

import './modules.scss';

function init() {
	const manager = new Plugin_Manager({
		container: '#ff_modules_manager',
		api: 'ff_plugin_modules_api',
		item_class: 'item_module',
		actions: ['install', 'uninstall', 'activate', 'deactivate', 'update'],
		refresh_button: true,
	});

	manager.hooks.on('item/init', init_item);

	manager.load_items({
		refresh: false,
		callback: () => {
			manager.init_refresh();
		},
	});
}
init();

function init_item(item) {
	dom.create('name', item.el, item.data.name);
	init_version(item);

	item.hooks.on('update', () => {
		item.el.dataset.installed = item.data.installed;
		item.el.dataset.active = item.data.active;
		item.el.dataset.outdated = item.data.outdated;
	});
}

function init_version(item) {
	const con = dom.create('version', item.el);

	if (item.data.outdated) {
		dom.create('num old', con, item.data.old_version);
		dom.create('num new', con, ' < ' + item.data.version);
	} else {
		dom.create('num', con, item.data.version);
	}
}
