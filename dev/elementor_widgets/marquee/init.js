import './marquee.scss';
import Marquee from './Marquee';

function init() {
	if (ff.is_elementor_edit) return;
	document.querySelectorAll('.ff_marquee').forEach(init_instance);
}
init();

function init_instance(el) {
	const settings = JSON.parse(el.dataset.settings);
	const speed = parseInt(settings.speed);
	new Marquee(el.querySelector('.items'), { duration: speed });
}
