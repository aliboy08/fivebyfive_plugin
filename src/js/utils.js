export function to_slug(text) {
	return text.toLowerCase().replaceAll(' ', '-');
}

export function format_date(date, format = 'm/d/y') {
	let y = date.getFullYear();

	let m = date.getMonth() + 1;
	if (m < 10) {
		m = '0' + m;
	}

	let d = date.getDate();
	if (d < 10) {
		d = '0' + d;
	}

	let date_format = {
		m,
		d,
		y,
	};

	let date_format_output = format;
	for (let key in date_format) {
		date_format_output = date_format_output.replace(key, date_format[key]);
	}

	return date_format_output;
}

export function get_el(element, multiple = false, parent = document) {
	if (typeof element === 'string') {
		if (multiple) {
			return parent.querySelectorAll(element);
		} else {
			return parent.querySelector(element);
		}
	}
	return element;
}

export function create_div(class_name, append_to = null, text = null) {
	const div = document.createElement('div');
	div.className = class_name;

	if (append_to) {
		if (typeof append_to === 'string') {
			append_to = document.querySelector(append_to);
		}
		append_to.append(div);
	}

	if (text) div.textContent = text;

	return div;
}

export function create_element(
	class_name,
	append_to = null,
	text = null,
	tag = 'div',
) {
	const element = document.createElement(tag);
	element.className = class_name;

	if (append_to) {
		if (typeof append_to === 'string') {
			append_to = document.querySelector(append_to);
		}
		append_to.append(element);
	}

	if (text) element.textContent = text;

	return element;
}

export function create_dom(html, target, type = 'append') {
	const temp = document.createElement('div');
	temp.innerHTML = html;

	const el = temp.children[0];

	if (target) {
		target[type](el);
		temp.remove();
	}

	return el;
}

export function create_dom_elements(html, target, type = 'append') {
	const temp = document.createElement('div');
	temp.innerHTML = html;

	const elements = [];

	for (const child of temp.children) {
		elements.push(child);
	}

	if (target) {
		elements.forEach((element) => {
			target[type](element);
		});
		temp.remove();
	}

	return elements;
}

export function lerp(a, b, t) {
	return a * (1 - t) + b * t;
}

export function onscreen(el, options = {}) {
	el = get_el(el);

	if (!el) return;

	const once = options.once ?? false;
	const observer_options = options.observer_options ?? {};

	const io_fn = (entries) => {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				if (typeof options.on === 'function') {
					options.on(entry, observer);
					if (once) {
						observer.unobserve(entry.target);
					}
				}
			} else {
				if (typeof options.off === 'function') {
					options.off(entry, observer);
				}
			}
		});
	};

	const observer = new IntersectionObserver(io_fn, observer_options);

	observer.observe(el);

	return observer;
}

export function items_animation_delay(
	container,
	selector = false,
	interval = 150,
) {
	container = get_el(container);

	const children = selector ? el.querySelectorAll(selector) : el.childNodes;

	let time = 0;
	children.forEach((item) => {
		item.style.animationDelay = time + 'ms';
		time += interval;
	});
}

export function scroll_to(el, offset = 0) {
	window.scrollTo({
		top: get_el(el).offsetTop + offset,
		behavior: 'smooth',
	});
}

export function scroll_to_items(items, options = {}) {
	items = get_el(items, true);
	const default_offset = options.default_offset ?? 0;

	const scroll_to = (item) => {
		const target = document.querySelector(item.attributes.href.value);
		if (!target) return;

		const offset = item.dataset.offset ?? default_offset;
		const top = target.offsetTop - parseInt(offset);

		window.scrollTo({
			top,
			behavior: 'smooth',
		});
	};

	items.forEach((item) => {
		item.addEventListener('click', (e) => {
			e.preventDefault();
			scroll_to(item);
		});
	});
}

export function animate_items(items, options = {}) {
	if (typeof items === 'string') {
		items = document.querySelectorAll(items);
	}

	const stagger = options.stagger ?? 50;
	const limit = options.limit ?? 30;
	const animation =
		options.animation ??
		'fade-in-bottom-short 0.6s cubic-bezier(0.39, 0.575, 0.565, 1) both';

	let stagger_increment = 0;
	let item_count = 0;
	items.forEach((item) => {
		if (item_count < limit) {
			item.style.animation = animation;
			item.style.animationDelay = stagger_increment + 'ms';
		}

		item.style.display = '';

		stagger_increment += stagger;
		item_count++;
	});
}

export function is_elementor_edit() {
	return window.location.search.indexOf('elementor-preview=') !== -1;
}

export function on_ready(fn) {
	if (document.readyState === 'loading') {
		// Loading hasn't finished yet
		document.addEventListener('DOMContentLoaded', () => fn());
	} else {
		// `DOMContentLoaded` has already fired
		fn();
	}
}

export function debounce(fn, timeout = 400) {
	let timer = null;
	return (...args) => {
		clearTimeout(timer);
		timer = setTimeout(() => {
			fn.apply(this, args);
		}, timeout);
	};
}

export function set_cookie(
	cookie_name,
	cookie_value,
	expiration_hours = 3,
	path = '/',
) {
	const d = new Date();
	d.setTime(d.getTime() + expiration_hours * 60 * 60 * 1000);
	const expires = 'expires=' + d.toUTCString();
	document.cookie =
		cookie_name + '=' + cookie_value + ';' + expires + ';path=' + path;
}

export function get_cookie(cname) {
	const name = cname + '=';
	const ca = document.cookie.split(';');
	for (let i = 0; i < ca.length; i++) {
		let c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return '';
}

export function remove_cookie(cookie_name, path = '/') {
	document.cookie =
		cookie_name +
		'=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=' +
		path +
		';';
}

export function copy_to_clipboard(text) {
	try {
		navigator.clipboard.writeText(text);
	} catch (e) {
		copy_to_clipboard_legacy_support(text);
	}
}

function copy_to_clipboard_legacy_support(text) {
	var el = document.createElement('textarea');
	el.value = text;
	el.setAttribute('readonly', '');
	el.style = { position: 'absolute', left: '-9999px' };
	document.body.appendChild(el);
	el.select();
	document.execCommand('copy');
	document.body.removeChild(el);
}

export function apply_query_string(key, value) {
	const url = new URL(window.location.href);

	if (!value) {
		url.searchParams.delete(key);
	} else {
		url.searchParams.set(key, value);
	}

	window.history.replaceState(null, document.title, url.href);
}

export function load_script(src, onload = null) {
	const script = document.createElement('script');
	script.src = src;
	if (typeof onload === 'function') {
		script.onload = onload;
	}
	document.body.appendChild(script);
}

export function load_style(href) {
	const link = document.createElement('link');
	link.rel = 'stylesheet';
	link.type = 'text/css';
	link.href = href;
	if (typeof onload === 'function') {
		link.onload = onload;
	}
	document.body.appendChild(link);
}

export function wrap_element(el, class_name, wrap_tag = 'div') {
	const wrapper = document.createElement(wrap_tag);
	wrapper.className = class_name;
	el.after(wrapper);
	wrapper.append(el);
	return wrapper;
}

export function wrap_elements({
	items,
	render = null,
	render_method = 'append',
	wrapper_tag = 'div',
	wrapper_class = '',
	item_tag = 'div',
	item_class = '',
}) {
	const wrapper = document.createElement(wrapper_tag);

	if (wrapper_class) {
		wrapper.className = wrapper_class;
	}

	if (render) {
		render[render_method](wrapper);
	}

	// items
	items.forEach((item) => {
		if (!item_class) {
			// add directly without container
			wrapper.append(item);
			return;
		}

		// create container for item
		const item_container = document.createElement(item_tag);
		item_container.className = item_class;
		item_container.append(item);
		wrapper.append(item_container);
	});

	return wrapper;
}

export function get_offset(element) {
	if (!element.getClientRects().length) {
		return { top: 0, left: 0 };
	}
	let rect = element.getBoundingClientRect();
	let win = element.ownerDocument.defaultView;
	return {
		top: rect.top + win.pageYOffset,
		left: rect.left + win.pageXOffset,
	};
}

export function get_last_child(container, class_name) {
	const last_index = container.children.length - 1;
	for (let i = last_index; i >= 0; i--) {
		if (container.children[i].classList.contains(class_name)) {
			return container.children[i];
		}
	}
	return container.children[last_index];
}

export function hover_class(el, class_name, duration) {
	let hover_timer = null;
	el.addEventListener('pointerenter', () => {
		clearTimeout(hover_timer);
		el.classList.add(class_name);
	});
	el.addEventListener('pointerleave', () => {
		hover_timer = setTimeout(() => {
			el.classList.remove(class_name);
		}, duration);
	});
}

export function custom_input_field(el, select = false, after_update = null) {
	const input = document.createElement('input');
	input.className = 'custom_input_field';
	input.type = 'text';
	input.value = el.textContent;

	el.style.display = 'none';
	el.after(input);

	input.focus();
	if (select) input.select();

	let updated = false;

	const update = () => {
		if (updated) return;
		updated = true;
		el.textContent = input.value;
		el.style.display = '';
		input.remove();

		if (typeof after_update === 'function') {
			after_update(input.value);
		}
	};

	input.addEventListener('change', update);
	input.addEventListener('blur', update);
}

export function string_to_int(text) {
	if (typeof text !== 'string') return text;
	if (text === '') return 0;
	text = text.replaceAll(',', '');
	text = text.replaceAll('$', '');
	text = text.replaceAll('+', '');
	text = text.replaceAll(' ', '');
	if (text === '') return 0;
	return parseInt(text);
}

export function string_to_num(text) {
	if (typeof text !== 'string') return text;
	if (text === '') return 0;
	text = text.replaceAll(',', '');
	text = text.replaceAll('$', '');
	text = text.replaceAll('+', '');
	text = text.replaceAll(' ', '');
	if (text === '') return 0;
	return parseFloat(text);
}

export function append_children(container, selectors, append_to) {
	selectors.forEach((selector) => {
		const el = container.querySelector(selector);
		if (!el) return;
		append_to.append(el);
	});
}

export function apply_el_styles(el, key) {
	if (!el[key]) return;
	for (const prop in el[key]) {
		el.style[prop] = el[key][prop];
	}
}

export function clear_el_styles(el, key) {
	if (!el[key]) return;
	for (const prop in el[key]) {
		el.style[prop] = '';
	}
	el[key] = null;
}

export function clear_el(el) {
	el = get_el(el);
	if (!el) return;
	while (el.firstChild) {
		el.removeChild(el.firstChild);
	}
}

export function hidden_iframe(id, src = '') {
	remove_el('#' + id);
	const iframe = document.createElement('iframe');
	if (src) iframe.src = src;
	if (id) iframe.id = id;
	iframe.height = 0;
	iframe.width = 0;
	iframe.style.border = 'none';
	iframe.style.position = 'absolute';
	iframe.style.bottom = 0;
	document.body.appendChild(iframe);
	return iframe;
}

export function remove_el(el) {
	if (typeof el === 'string') {
		el = document.querySelector(el);
	}
	if (!el) return;
	el.remove();
}

export function get_aspect_ratio(
	width_initial,
	height_initial,
	width_max,
	height_max,
) {
	const ratio = Math.min(
		width_max / width_initial,
		height_max / height_initial,
	);
	return { width: width_initial * ratio, height: height_initial * ratio };
}

const amount_formatter = Intl.NumberFormat('en-US', {
	maximumFractionDigits: 2,
});
export function format_amount(amount) {
	if (typeof amount === 'string') {
		amount = amount.replace(',', '');
	}
	return amount_formatter.format(amount);
}

export function normalize_text(input) {
	return input.toLowerCase().replace(/[^0-9a-z]/gi, '');
}

export function is_child(target, parent) {
	if (target.nodeName === 'BODY') return false;

	if (target == parent || target.parentElement == parent) {
		return true;
	}

	return is_child(target.parentElement, parent);
}

export function outside_click_handler(el, on_outside_click) {
	const click_handler = (e) => {
		if (!is_child(e.target, el)) {
			const proceed = on_outside_click();
			if (typeof proceed === 'undefined') {
				end();
			} else {
				if (proceed) {
					end();
				}
			}
		}
	};

	const start = () => {
		setTimeout(() => {
			document.addEventListener('click', click_handler);
		}, 100);
	};

	const end = () => {
		document.removeEventListener('click', click_handler);
	};

	return { start, end };
}

export function animate_opacity(el, callback, duration = 300) {
	el.style.transition = `opacity ${duration}ms opacity`;
	el.style.opacity = 0;

	setTimeout(() => {
		if (typeof callback === 'function') {
			callback();
		}

		setTimeout(() => {
			el.style.opacity = 1;

			setTimeout(() => {
				el.style.transition = '';
				el.style.opacity = '';
			}, duration);
		}, 1);
	}, duration);
}

export function hide(el, callback, duration = 200) {
	el = get_el(el);
	if (!el) return;

	el.style.transition = `opacity ${duration}ms ease`;
	el.style.pointerEvents = 'none';
	el.style.opacity = 0;

	setTimeout(() => {
		el.style.display = 'none';
		el.style.transition = '';
		el.style.pointerEvents = '';
		el.style.opacity = '';
		if (typeof callback === 'function') callback();
	}, duration);
}

export function show(el, args = {}) {
	el = get_el(el);
	if (!el) return;

	const animation_name = args.animation_name ?? 'fade-in';
	const easing = 'cubic-bezier(0.39, 0.575, 0.565, 1)';
	const duration = args.duration ?? 300;
	const callback = args.callback ?? null;

	let animation = `${animation_name} ${duration}ms ${easing}`;
	if (args.animation) {
		animation = args.animation;
	}

	el.style.animation = animation;
	el.style.display = '';

	setTimeout(() => {
		el.style.animation = '';
		if (typeof callback === 'function') callback();
	}, duration);
}

export function update_dom(html, target) {
	const temp = document.createElement('div');
	temp.innerHTML = html;

	const el = temp.children[0];

	target.after(el);
	temp.remove();
	target.remove();

	return el;
}

export function is_touch_device() {
	return (
		'ontouchstart' in window ||
		navigator.maxTouchPoints > 0 ||
		navigator.msMaxTouchPoints > 0
	);
}

let decode_input = null;
export function decode_html_entitites(text) {
	if (!decode_input) {
		decode_input = document.createElement('textarea');
	}
	decode_input.innerHTML = text;
	return decode_input.value;
}

export function val(value, fallback = null, apply_fn = null) {
	if (typeof value !== 'undefined') {
		if (typeof apply_fn === 'function') {
			return apply_fn(value);
		}
		return value;
	}
	return fallback;
}

export function set_defaults(defaults, settings = {}) {
	for (const key in defaults) {
		if (typeof settings[key] === 'undefined') {
			settings[key] = defaults[key];
		}
	}
	return settings;
}

export function set_overrides(overrides, settings = {}) {
	for (const key in settings) {
		if (typeof overrides[key] !== 'undefined') {
			settings[key] = overrides[key];
		}
	}
	return settings;
}

export function hover_intent(el, duration = 300, hover_class = 'hover') {
	el = get_el(el);

	if (!el || typeof el.hover_intent !== 'undefined') return;

	let timeout;

	el.addEventListener('pointerenter', () => {
		clearTimeout(timeout);
		el.classList.add(hover_class);
	});

	el.addEventListener('pointerleave', () => {
		clearTimeout(timeout);
		timeout = setTimeout(() => {
			el.classList.remove(hover_class);
		}, duration);
	});
}

export function ff_plugin_ajax(action, payload = {}, callback = null) {
	payload.nonce = ff_plugin.nonce;
	const request = fetch(`${ff_plugin.ajax_url}?action=${action}`, {
		method: 'POST',
		body: JSON.stringify(payload),
	});
	if (typeof callback === 'function') {
		request.then((res) => res.json()).then((res) => callback(res));
	}
}

export function check_ready(
	target,
	on_ready,
	on_fail,
	interval = 300,
	limit = 60000,
) {
	let loop;
	let t = 0;

	const check = () => {
		if (t >= limit) {
			clearInterval(loop);
			if (typeof on_fail === 'function') on_fail();
		}

		if (typeof target !== 'undefined') {
			clearInterval(loop);
			if (typeof on_ready === 'function') on_ready();
		}

		t += interval;
	};

	loop = setInterval(check, interval);
}

export function dispatch_event(name, args = null) {
	const e = new Event(name);
	e.args = args;
	document.dispatchEvent(e);
}
