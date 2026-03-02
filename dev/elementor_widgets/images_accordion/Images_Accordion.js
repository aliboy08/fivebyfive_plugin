export default class Images_Accordion {
	constructor(el) {
		this.el = el;
		this.init();
	}

	init() {
		this.settings = JSON.parse(this.el.dataset.widget_settings);

		this.settings.mode = this.settings.mode ?? 'click';
		this.settings.animation_duration = this.settings.animation_duration ?? 800;

		this.current = this.el.querySelector('.item.current');

		this.el.querySelectorAll('.item').forEach((item) => {
			item.content_con = item.querySelector('.content_con');

			item.style.transition = this.settings.animation_duration + 'ms ease';

			if (this.settings.mode == 'click') {
				// mode = click
				item.addEventListener('click', () => {
					this.set_current(item);
				});
			} else {
				// mode = hover
				item.addEventListener('mouseenter', () => {
					this.set_current(item);
				});
			}
		});
	}

	set_current(item) {
		this.unset_current();

		this.current = item;
		this.current.classList.add('current');

		item.content_con.style.display = '';

		setTimeout(() => {
			this.current.classList.add('transition_complete');
		}, this.settings.animation_duration);
	}

	unset_current() {
		if (!this.current) return;

		this.current.content_con.style.display = 'none'; // instant hide previous content

		this.current.classList.remove('current');
		this.current.classList.remove('transition_complete');
	}
}
