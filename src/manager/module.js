import { global_hooks } from 'src/globals';
import { create_div, ff_plugin_ajax } from 'js/utils';
import Hooks from 'js/hooks';

class Module {
	constructor(args) {
		this.hooks = new Hooks();

		this.el = args.el;
		this.data = args.data;
		this.installed = args.data.installed ?? false;
		this.active = args.data.active ?? false;
		this.outdated = args.data.outdated ?? false;
		this.loading = false;

		this.init_html();
	}

	init_html() {
		create_div('name', this.el, this.data.name);

		const version_con = create_div('version', this.el);
		this.version_el = create_div('num', version_con, this.data.version);

		this.actions_con = create_div('actions', this.el);
	}

	update_version(version) {
		if (!this.outdated) return;
		this.version_el.textContent = version;
	}

	async do_action(type) {
		if (this.loading) return;
		this.loading = true;

		return new Promise((resolve) => {
			ff_plugin_ajax(
				'ff_plugin_manager_action',
				{ action: type, module: this.data },
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

global_hooks.on('ff/module/init', init);
function init(item_args) {
	const item = new Module(item_args);
	item_args.el.module = item;

	init_deactivate(item);
	init_activate(item);
	init_uninstall(item);
	init_install(item);

	item.update();
}

function init_deactivate(item) {
	const btn = create_div('btn deactivate', item.actions_con, 'Deactivate');

	const show = () => {
		return item.active && item.installed;
	};

	item.hooks.on('update', () => {
		btn.style.display = show() ? '' : 'none';
	});

	btn.addEventListener('click', async () => {
		await do_action('deactivate', item, btn);
		window.location.reload();
	});
}

function init_activate(item) {
	const btn = create_div('btn activate', item.actions_con, 'Activate');

	const show = () => {
		return !item.active && item.installed;
	};

	item.hooks.on('update', () => {
		btn.style.display = show() ? '' : 'none';
	});

	btn.addEventListener('click', async () => {
		await do_action('activate', item, btn);
		window.location.reload();
	});
}

function init_uninstall(item) {
	const btn = create_div('btn uninstall', item.actions_con, 'Uninstall');

	const show = () => {
		return item.installed && !item.active;
	};

	item.hooks.on('update', () => {
		btn.style.display = show() ? '' : 'none';
	});

	btn.addEventListener('click', () => do_action('uninstall', item, btn));
}

function init_install(item) {
	const btn = create_div('btn install', item.actions_con, 'Install');

	const show = () => {
		return !item.installed;
	};

	item.hooks.on('update', () => {
		btn.style.display = show() ? '' : 'none';
	});

	btn.addEventListener('click', () => do_action('install', item, btn));
}

async function do_action(type, item, btn) {
	btn.classList.add('loading');
	await item.do_action(type);
	btn.classList.remove('loading');
}
