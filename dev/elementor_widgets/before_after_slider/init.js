import './before_after_slider.scss';
import Before_After_Slider from './Before_After_Slider';

function init() {
	if (ff.is_elementor_edit) return;
	document.querySelectorAll('.ff_before_after_slider').forEach(init_instance);
}
init();

function init_instance(el) {
	if (typeof el.before_after_slider !== 'undefined') return;
	el.before_after_slider = new Before_After_Slider(el);
}
