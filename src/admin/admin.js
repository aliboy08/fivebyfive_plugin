import { dom } from 'js/dom';
import { ff_plugin_ajax, init_button_loading } from 'js/utils';
console.log('admin.js');

const container = dom.get('#ff_plugin_admin_page');
init_check_updates();

function init_check_updates() {
	const btn = dom.create(
		'button-primary',
		container,
		'Check Updates',
		'button',
	);

	init_button_loading(btn);

	btn.onclick = () => {
		btn.loading();
		btn.remove();

		ff_plugin_ajax(
			'ff_plugin_admin_api',
			{ action: 'check_updates' },
			(res) => {
				btn.loading_end();
				init_dist_update(res);
			},
		);
	};
}

function init_dist_update(res) {
	if (res?.repo_data?.dist_version === res?.site_data?.dist_version) return;

	const btn = dom.create('button-primary', container, 'Update Dist', 'button');

	init_button_loading(btn);

	btn.onclick = () => {
		btn.loading();
		btn.remove();

		ff_plugin_ajax(
			'ff_plugin_admin_api',
			{ action: 'update_dist', ver: res.repo_data.dist_version },
			(res) => {
				console.log(res);
				btn.loading_end();
			},
		);
	};
}
