import { Controller } from '@hotwired/stimulus';
import $ from 'jquery';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        // Autohide element after 3 seconds
        setTimeout(() => $(this.element).fadeOut(), 3 * 1000);
    }
}
