import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        scrollMargin: { type: Number, default: 50 },
        duration: { type: Number, default: 3 },
        cssClasses: { type: Array, default: ["border", "border-2", "border-success", "rounded", "shadow", "my-3"] },
    };

    connect() {
        this.element.classList.add(...this.cssClassesValue);

        this.element.style.scrollMargin = this.scrollMarginValue + 'px';
        this.element.scrollIntoView({ behavior: 'smooth' });

        setTimeout(() => {
            this.element.classList.remove(...this.cssClassesValue);
        }, this.durationValue * 1000);
    }
}
