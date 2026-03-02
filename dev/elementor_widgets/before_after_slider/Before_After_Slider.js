export default class Before_After_Slider {
    
    constructor(el){
        this.el = el;
        this.init();
        this.init_events();
    }

    init(){

        this.before = this.el.querySelector('.before_image');
        this.handle = this.el.querySelector('.handle');

        this.initial_styles();
    }

    init_events(){
        this.handle.addEventListener('pointerdown',()=>this.drag_start());
    }

    drag_start(){

        this.handle.classList.add('active');

        this.pos = this.el.getBoundingClientRect();

        const drag = (e)=>{
            this.update(e.pageX)
        }

        const touch_drag = (e)=>{
            this.update(e.changedTouches[0].pageX)
        }
        
        const drag_end = (e)=>{

            this.handle.classList.remove('active');

            document.removeEventListener('pointerup', drag_end)
            document.removeEventListener('pointermove', drag);
            document.removeEventListener('touchmove', touch_drag);
            document.removeEventListener('touchend', drag_end);
            
        }
        
        document.addEventListener('pointerup', drag_end)
        document.addEventListener('pointermove', drag);
        document.addEventListener('touchmove', touch_drag)
        document.addEventListener('touchend', drag_end)
    }
    
    update(x){

        x -= this.pos.left;
        
        const dx = Math.max(0,(Math.min(x, this.el.offsetWidth)));

        this.handle.style.left = dx + 'px';
        this.before.style.width = dx + 'px';
    }
    
    initial_styles(){

        this.pos = this.el.getBoundingClientRect();
        
        const img = this.before.getElementsByTagName('img')[0];
        img.style.width = this.pos.width + 'px';
        img.style.height = this.pos.height + 'px';
    }
    
}