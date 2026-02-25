export default class Hooks {
	constructor() {
		this.init();
	}

	init() {
		this.actions = {};
		this.filters = {};
		this.queue = {};
	}

	on(action_name, fn, options = {}) {
		if (typeof fn !== 'function') return;

		const priority = options.priority ?? 10;

		const action = {
			fn,
			priority,
			once: options.once ?? false,
		};

		if (!this.actions[action_name]) {
			this.actions[action_name] = {};
		}

		if (!this.actions[action_name][priority]) {
			this.actions[action_name][priority] = [];
		}

		this.actions[action_name][priority].push(action);
	}

	do(action_name, args = {}) {
		if (!this.actions[action_name]) return;

		for (const priority in this.actions[action_name]) {
			const actions = this.actions[action_name][priority];

			actions.forEach((action) => {
				action.fn(args);

				if (action.once) {
					actions.splice(actions.indexOf(action), 1);
				}
			});
		}
	}

	apply_filter(key, value = null, args = null) {
		if (!this.filters[key]) return value;

		for (const priority in this.filters[key]) {
			const filters = this.filters[key][priority];

			for (const filter of filters) {
				value = filter.fn(value, args);

				if (filter.once) {
					filters.splice(filters.indexOf(filter), 1);
				}
			}
		}

		return value;
	}

	add_filter(key, fn, options = {}) {
		if (typeof fn !== 'function') return;

		if (!this.filters[key]) {
			this.filters[key] = {};
		}

		const priority = options.priority ?? 10;

		const getter = {
			fn,
			priority,
			once: options.once ?? false,
		};

		if (!this.filters[key][priority]) {
			this.filters[key][priority] = [];
		}

		this.filters[key][priority].push(getter);
	}

	clear(options = null) {
		if (!options) {
			return this.init();
		}

		if (options.action) {
			this.actions[options.action] = {};
		}

		if (options.queue) {
			this.queue[options.queue] = {};
		}

		if (options.getter) {
			this.filters[options.getter] = {};
		}
	}

	on_queue(key, fn) {
		if (!this.queue[key]) {
			this.queue[key] = {
				args: null,
				ready: false,
				actions: [],
			};
		}

		const queue = this.queue[key];
		queue.actions.push(fn);

		if (!queue.ready) return;

		queue.actions.forEach((action) => {
			action(queue.args);
		});

		// queue.actions = [];
	}

	do_queue(key, args) {
		let queue = this.queue[key];

		if (!queue) {
			this.queue[key] = {
				args,
				ready: true,
				actions: [],
			};
			return;
		}

		queue.ready = true;

		queue.actions.forEach((action) => {
			action(args);
		});

		// queue.actions = [];
	}
}
