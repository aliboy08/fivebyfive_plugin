import './images_accordion.scss';
import Images_Accordion from './Images_Accordion';

document.querySelectorAll('.ff-images-accordion').forEach(init);

function init(el) {
	new Images_Accordion(el);
}
