import { create_div, clear_el, ff_plugin_ajax } from 'js/utils';
import { global_hooks } from 'src/globals';

import './manager.scss';
import './module';

ff_plugin_ajax(
	'ff_plugin_manager_action',
	{ action: 'get_modules' },
	render_modules,
);

function render_modules(modules) {
	const container = document.querySelector('#ff_modules_manager');

	clear_el(container);

	const els = [];
	modules.forEach((m) => {
		const el = create_div('module', container);
		global_hooks.do('ff/module/init', {
			el,
			data: m,
		});
		els.push(el);
	});

	els.forEach((el) => {
		if (el.module.installed) {
			container.prepend(el);
		}
	});
}
