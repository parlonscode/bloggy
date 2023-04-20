import { Controller } from '@hotwired/stimulus';
import $ from 'jquery';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = { 
        duration: { type: Number, default: 3 },
        scrollTargetId: String
    };

    connect() {
        setTimeout(() => {
            $(this.element).fadeOut();

            if (this.scrollTargetIdValue !== '') {
                document.getElementById(this.scrollTargetIdValue)
                    .scrollIntoView({ behavior: 'smooth' });
            }
        }, this.durationValue * 1000);
    }
}
