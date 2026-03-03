import { dom } from 'js/dom';
import { ff_plugin_ajax, init_button_loading, group_items } from 'js/utils';
import Hooks from 'js/hooks';
import './categories_populate.scss';

const res_el = dom.get('#cp_result');

const hooks = new Hooks();

const prepare_btn = dom.get('#cp_prepare_btn');
init_button_loading(prepare_btn);
prepare_btn.onclick = prepare_data;

function prepare_data() {
	prepare_btn.loading();

	const payload = {
		action: 'get_data',
		post_type: dom.get('#cp_post_type').value,
		items: get_items(),
	};

	ff_plugin_ajax('ff_plugin_categories_populate_api', payload, (res) => {
		prepare_btn.loading_end();
		prepare_data_result(res);
	});
}

function prepare_data_result(items) {
	dom.clear(res_el);

	const valid_items = [];

	items.forEach((item) => {
		render_item(item);

		if (!item.id) {
			dom.create('error', item.el, 'post not found');
			item.el.dataset.state = 'error';
		} else {
			valid_items.push(item);
		}
	});

	if (valid_items.length) {
		init_proceed(valid_items);
	}
}

function render_item(item) {
	const el = dom.create('item', res_el);

	dom.create('title', el, item.title);

	if (item?.categories?.length) {
		dom.create('categories', el, item.categories.join(', '));
	}

	item.el = el;
}

function init_proceed(items) {
	const btn = dom.create('button-primary', null, 'Proceed', 'button');
	res_el.before(btn);
	init_button_loading(btn);

	btn.onclick = () => {
		btn.loading();
		dom.clear(res_el);
		items.forEach(render_item);
		process_items(items);
	};

	hooks.on('progress/complete', () => {
		btn.loading_end();
	});
}

async function process_items(items) {
	init_progress();

	const groups = group_items(items, 5);

	const taxonomy = dom.get('#cp_taxonomy').value;

	const process_group = (items_data) => {
		return new Promise((resolve) => {
			const payload = {
				action: 'update_items',
				items: items_data,
				taxonomy,
			};
			ff_plugin_ajax('ff_plugin_categories_populate_api', payload, (res) => {
				console.log('update_res', res);
				setTimeout(resolve, 500);
			});
		});
	};

	let progress = 0;
	let i = 0;

	for (const group of groups) {
		const items_data = [];

		group.forEach((item) => {
			const item_data = { ...item };
			delete item_data.el;
			items_data.push(item_data);
		});

		await process_group(items_data);
		i++;

		progress = (i / groups.length) * 100;

		hooks.do('progress/update', progress);
	}
}

function get_items() {
	let input_titles = dom.get('#cp_titles').value.split('\n');
	let input_categories = dom.get('#cp_categories').value.split('\n');

	const items = [];
	for (let i = 0; i < input_titles.length; i++) {
		let categories = input_categories[i];
		if (categories) categories = categories.split('\n');

		items.push({
			title: input_titles[i].trim(),
			categories,
		});
	}
	return items;
}

function init_progress() {
	const el = dom.create('cp_progress');
	const fill = dom.create('fill', el);
	res_el.before(el);

	const update = (percent) => {
		fill.style.width = `${percent}%`;
		if (percent === 100) {
			hooks.do('progress/complete');
		}
	};

	hooks.on('progress/update', update);
}
