import { dom } from 'js/dom';
import { Plugin_Manager } from 'src/manager/manager';

import './elementor_widgets.scss';

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
		callback: () => {
			manager.init_refresh();
		},
	});
}
init();

function init_item(item) {
	dom.create('name', item.el, item.data.name);
	dom.create('slug', item.el, `(${item.data.slug})`);

	item.hooks.on('update', () => {
		item.el.dataset.installed = item.data.installed;
		item.el.dataset.active = item.data.active;
		item.el.dataset.outdated = item.data.outdated;
	});
}
