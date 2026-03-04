import { dom } from 'js/dom';
import { Plugin_Manager } from 'src/manager/manager';

import './main.scss';

function init() {
	const manager = new Plugin_Manager({
		container: '#ff_elementor_widgets',
		api: 'ff_plugin_elementor_widgets_api',
		item_class: 'item_widget',
		actions: ['install', 'uninstall', 'activate', 'deactivate'],
		refresh_button: true,
	});

	manager.hooks.on('item/init', init_item);

	manager.load_items({
		refresh: false,
		callback: (items) => {
			console.log(items);
			manager.init_refresh();
		},
	});
}
init();

function init_item(item) {
	dom.create('name', item.el, item.data.name);

	init_version(item);
	init_lock(item);

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

function init_lock(item) {
	const btn = dom.create('lock', item.el);

	const update_other_buttons = () => {
		if (!item.data.locked) return;

		if (item.uninstall_btn) {
			item.uninstall_btn.style.display = 'none';
		}

		if (item.update_btn) {
			item.update_btn.style.display = 'none';
		}
	};

	const update = () => {
		btn.style.display = item.data.installed ? '' : 'none';
		btn.dataset.state = item.data.locked ? 1 : 0;
		setTimeout(update_other_buttons, 100);
	};

	btn.onclick = () => {
		item.data.locked = !(item.data.locked ?? false);

		item.do_action({
			type: 'update_item',
			item: item.data,
		});
	};

	item.hooks.on('update', update);
}
