export const dom = {
	get,
	create,
	create_html,
	clear,
};

function get(el, multiple = false, parent = document) {
	if (typeof el === 'string') {
		if (multiple) {
			return parent.querySelectorAll(el);
		} else {
			return parent.querySelector(el);
		}
	}
	return el;
}

function create(class_name = null, append_to = null, html = null, tag = 'div') {
	const el = document.createElement(tag);

	if (class_name) {
		el.className = class_name;
	}

	if (append_to) {
		if (typeof append_to === 'string') {
			append_to = document.querySelector(append_to);
		}
		append_to.append(el);
	}

	if (html) el.innerHTML = html;

	return el;
}

function create_html(html, target, type = 'append') {
	const temp = document.createElement('div');
	temp.innerHTML = html;

	const el = temp.children[0];

	if (target) {
		target[type](el);
		temp.remove();
	}

	return el;
}

function clear(el) {
	el = get(el);
	if (!el) return;
	while (el.firstChild) {
		el.removeChild(el.firstChild);
	}
}
