import { ff_plugin_ajax } from 'js/utils';
import { dom } from 'js/dom';
import Hooks from 'js/hooks';

import './manager.scss';

export class Plugin_Manager {
	constructor(args) {
		this.hooks = new Hooks();
		this.args = args;
		this.container = dom.get(args.container);
	}

	init_refresh() {
		const btn = dom.create('button-primary', null, 'Refresh', 'button');

		this.container.after(btn);

		let spinner;

		btn.onclick = () => {
			if (spinner) return;
			spinner = dom.create('spinner show');
			btn.after(spinner);

			this.load_items({
				refresh: true,
				callback: () => {
					btn.remove();
					spinner.remove();
				},
			});
		};
	}

	update_items(items) {
		this.items = [];
		dom.clear(this.container);
		items.forEach((item) => this.init_item(item));
		sort_items(this);
	}

	load_items(args = {}) {
		ff_plugin_ajax(
			this.args.api,
			{ action: 'get_items', refresh: args.refresh ?? false },
			(items) => {
				this.update_items(items);
				if (args.callback) args.callback();
			},
		);
	}

	init_item(item_data) {
		const el = dom.create('item', this.container);

		const item = {
			data: item_data,
			el,
			hooks: new Hooks(),
			update: () => {
				item.hooks.do('update');
			},
		};

		item.el = el;

		if (this.args.item_class) {
			el.classList.add(this.args.item_class);
		}

		this.hooks.do('item/init', item);

		this.init_item_actions(item);

		item.update();

		this.items.push(item);
	}

	init_item_actions(item) {
		let loading = false;

		item.do_action = (args) => {
			if (loading) return;
			loading = true;

			if (args.add_loading_class) {
				args.add_loading_class.classList.add('loading');
			}

			ff_plugin_ajax(
				this.args.api,
				{ action: args.type, item: item.data },
				(res) => {
					console.log('action:res', res);

					if (args.callback) {
						args.callback(res);
					}

					if (res.error) {
						alert(res.error);
					} else if (args.on_ok) {
						args.on_ok(res);
					}

					loading = false;

					if (args.add_loading_class) {
						args.add_loading_class.classList.remove('loading');
					}

					item.update();
				},
			);
		};

		item.actions_con = dom.create('actions', item.el);
		init_activate(item, this);
		init_deactivate(item, this);
		init_install(item, this);
		init_uninstall(item, this);
		init_update(item, this);
	}
}

function init_install(item, main) {
	if (!main.args.actions.includes('install')) return;

	const btn = dom.create('btn install', item.actions_con, 'Install');

	item.hooks.on('update', () => {
		btn.style.display = !item.data.installed ? '' : 'none';
	});

	btn.addEventListener('click', () => {
		item.do_action({
			type: 'install',
			add_loading_class: btn,
			on_ok: (res) => {
				item.data.installed = true;
			},
		});
	});
}

function init_uninstall(item, main) {
	if (!main.args.actions.includes('uninstall')) return;

	const btn = dom.create('btn uninstall', item.actions_con, 'Uninstall');

	item.hooks.on('update', () => {
		btn.style.display = item.data.installed && !item.data.active ? '' : 'none';
	});

	btn.addEventListener('click', () => {
		item.do_action({
			type: 'uninstall',
			add_loading_class: btn,
			on_ok: () => {
				item.data.installed = false;
			},
		});
	});
}

function init_activate(item, main) {
	if (!main.args.actions.includes('activate')) return;

	const btn = dom.create('btn activate', item.actions_con, 'Activate');

	item.hooks.on('update', () => {
		btn.style.display = !item.data.active && item.data.installed ? '' : 'none';
	});

	btn.addEventListener('click', () => {
		item.do_action({
			type: 'activate',
			add_loading_class: btn,
			on_ok: () => {
				item.data.active = true;
				window.location.reload();
			},
		});
	});
}

function init_deactivate(item, main) {
	if (!main.args.actions.includes('deactivate')) return;

	const btn = dom.create('btn deactivate', item.actions_con, 'Deactivate');

	item.hooks.on('update', () => {
		btn.style.display = item.data.active && item.data.installed ? '' : 'none';
	});

	btn.addEventListener('click', () => {
		item.do_action({
			type: 'deactivate',
			add_loading_class: btn,
			on_ok: () => {
				item.data.active = false;
				window.location.reload();
			},
		});
	});
}

function init_update(item, main) {
	if (!main.args.actions.includes('update')) return;

	const btn = dom.create('btn update', item.actions_con, 'Update');

	item.hooks.on('update', () => {
		btn.style.display = item.data.outdated ? '' : 'none';
	});

	btn.addEventListener('click', () => {
		item.do_action({
			type: 'update',
			add_loading_class: btn,
			on_ok: () => {
				item.data.outdated = false;
				window.location.reload();
			},
		});
	});
}

function sort_items(main) {
	main.items.forEach((item) => {
		if (item.data.installed) {
			main.container.prepend(item.el);
		}
	});
	main.items.forEach((item) => {
		if (item.data.active) {
			main.container.prepend(item.el);
		}
	});
}
