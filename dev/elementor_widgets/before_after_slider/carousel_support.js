function init() {
	const container = document.querySelector(
		'.before_after_slider_carousel .swiper',
	);
	if (!container) return;

	disable_carousel_drag(container.swiper);
	on_change_update_before_after(container.swiper);
}
setTimeout(init, 1000);

function disable_carousel_drag(swiper) {
	swiper.allowTouchMove = false;
}

function on_change_update_before_after(swiper) {
	swiper.on('slideChangeTransitionEnd', () => {
		let current_slide = swiper.slides[swiper.realIndex];
		let el = current_slide.querySelector('.ff_before_after_slider');
		if (!el || typeof el.before_after_slider == 'undefined') return;

		// recalculate before/after slider
		el.before_after_slider.calculate();
	});
}
