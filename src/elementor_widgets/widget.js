import { global_hooks } from 'src/globals';
import { ff_plugin_ajax } from 'js/utils';
import { dom } from 'js/dom';
import Hooks from 'js/hooks';

class Widget {
	constructor(args) {
		this.hooks = new Hooks();

		this.el = args.el;
		this.data = args.data;
		this.installed = args.data.installed ?? false;
		this.active = args.data.active ?? false;
		this.loading = false;

		this.init_html();
	}

	init_html() {
		dom.create('name', this.el, this.data.name);
		this.actions_con = dom.create('actions', this.el);
	}

	async do_action(type) {
		if (this.loading) return;
		this.loading = true;

		return new Promise((resolve) => {
			ff_plugin_ajax(
				'ff_plugin_manager_action',
				{ action: type, item: this.data },
				(res) => {
					this.action_response(resolve, res, type);
				},
			);
		});
	}

	action_response(resolve, res, type) {
		switch (type) {
			case 'install':
				this.installed = true;
				break;
			case 'uninstall':
				this.installed = false;
				break;
			case 'activate':
				this.active = true;
				break;
			case 'deactivate':
				this.active = false;
				break;
		}

		this.update();
		resolve(res);

		setTimeout(() => {
			this.loading = false;
		}, 100);
	}

	update() {
		this.hooks.do('update');

		this.el.dataset.installed = this.installed;
		this.el.dataset.active = this.active;
		this.el.dataset.outdated = this.outdated;
	}
}

global_hooks.on('ff_plugin/widget/init', init);
function init(item_args) {
	const item = new Widget(item_args);
	item_args.el.widget = item;

	init_deactivate(item);
	init_activate(item);
	init_uninstall(item);
	init_install(item);

	item.update();
}

function init_deactivate(item) {
	const btn = dom.create('btn deactivate', item.actions_con, 'Deactivate');

	const show = () => {
		return item.active && item.installed;
	};

	item.hooks.on('update', () => {
		btn.style.display = show() ? '' : 'none';
	});

	btn.addEventListener('click', () => do_action('deactivate', item, btn, true));
}

function init_activate(item) {
	const btn = dom.create('btn activate', item.actions_con, 'Activate');

	const show = () => {
		return !item.active && item.installed;
	};

	item.hooks.on('update', () => {
		btn.style.display = show() ? '' : 'none';
	});

	btn.addEventListener('click', () => do_action('activate', item, btn, true));
}

function init_uninstall(item) {
	const btn = dom.create('btn uninstall', item.actions_con, 'Uninstall');

	const show = () => {
		return item.installed && !item.active;
	};

	item.hooks.on('update', () => {
		btn.style.display = show() ? '' : 'none';
	});

	btn.addEventListener('click', () => do_action('uninstall', item, btn));
}

function init_install(item) {
	const btn = dom.create('btn install', item.actions_con, 'Install');

	const show = () => {
		return !item.installed;
	};

	item.hooks.on('update', () => {
		btn.style.display = show() ? '' : 'none';
	});

	btn.addEventListener('click', () => do_action('install', item, btn));
}

async function do_action(type, item, btn, reload = false) {
	btn.classList.add('loading');

	await item.do_action(type);

	if (reload) {
		window.location.reload();
	}

	btn.classList.remove('loading');
}
