import { create_div, onscreen, get_el } from 'js/utils';

export default class Marquee {
    
    constructor(container, options = {}){

        this.options = options;
        this.container = get_el(container);
        this.duration = options.duration ?? 2000;
        this.clone_count = options.clone ?? 1;
        this.direction = options.direction ?? 'left';
        this.state = '';
        
        this.init();
    }

    init(){
        this.loop_timeout = null;

        this.create_container_wrapper();
        this.calculate_width();
        this.calculate_translate();
        this.clone_items();

        this.animation_duration = this.duration * this.container.childNodes.length;

        onscreen(this.container, {
            once: true,
            on:()=>{
                this.start();
            },
        })
    }

    calculate_translate(){
        this.translateX = this.container.offsetWidth;
        this.translateX *= -1;
        if( this.direction == 'right' ) {
            this.translateX *= -1;
        }
    }

    calculate_width(){
        
        let total_width = 0;
        this.container.childNodes.forEach(item=>{
            if(!item.style) return;
            total_width += item.offsetWidth;
            item.style.flexShrink = 0;
        });

        let gap = window.getComputedStyle(this.container).columnGap;
        if( gap ) {
            let gap_width = parseInt(gap) * (this.container.children.length);
            if( !isNaN(gap_width) ) {
                total_width += gap_width;
            }
        }

        this.container.style.flexWrap = 'nowrap';
        this.container.style.minWidth = total_width + 'px';
    }

    create_container_wrapper(){
        if( typeof this.container_wrapper !== 'undefined' ) return;
        this.container_wrapper = create_div('inifinte_carousel_wrapper');
        this.container_wrapper.style.overflow = 'hidden';
        this.container_wrapper.style.display = 'flex';
        this.container.after(this.container_wrapper);
        this.container_wrapper.append(this.container);
    }

    clone_items(){
        let initial_items = [];
        this.container.childNodes.forEach(item=>{
            initial_items.push(item);
        });

        for( let i = 0; i < this.clone_count; i++ ) {
            initial_items.forEach(item=>{
                let clone = item.cloneNode();
                clone.innerHTML = item.innerHTML;
                this.container.append(clone);
            });
        }
    }

    play(){
        this.container.style.transition = 'transform '+ this.animation_duration +'ms linear';
        this.container.style.transform = 'translateX('+ this.translateX +'px)';
    }

    reset(){
        this.container.style.transition = 'none';
        this.container.style.transform = 'translateX(0)';
    }

    loop(){
        clearTimeout(this.loop_timeout);
        this.play();
        this.loop_timeout = setTimeout(()=>{
            this.reset();
            setTimeout(()=>{
                this.loop();
            }, 5);
        }, this.animation_duration);
    }

    start(){
        this.state = 'started';
        this.loop();
    }

    stop(){
        this.state = 'stopped';
        clearTimeout(this.loop_timeout);
        this.reset();
    }
}