import {API_POINTS} from './config';
import {DEFAULT_SELECTORS} from './config';

export class GeoWidgetPreview {
    constructor(node) {
        this.wrapper = node;
    }

    async renderFromCode(code) {
        try {
            if (!code) return false;

            const response = await fetch(`${API_POINTS}/${code}`);

            if (!response.ok) throw Error(response.statusText);

            const data = await response.json();

            this.renderTemplate(data);
        } catch (error) {
            console.error(error);
        }
    }

    renderTemplate(data) {
        if (!this.wrapper) {
            throw new Error('BitBagInPostPlugin - The specified wrapper node could not be found in the DOM');
        }

        if (!data) {
            return;
        }

        this.wrapper.innerHTML = '';
        this.wrapper.insertAdjacentHTML(
            'beforeend',
            `
            <img src="${data.image_url}" class="bb-inpost-point-img"/>
            <div class="bb-inpost-point-desc" ${DEFAULT_SELECTORS.previewRaw} ${DEFAULT_SELECTORS.testPointPatter.replace('%point_name%', data.name)}>
                <b>
                    ${data.name}
                </b>
                <p>
                    ${data.address.line1}<br>
                    ${data.address.line2}<br>
                    <small>${data.location_description}</small>
                </p>
            </div>
        `
        );
    }
}

export default GeoWidgetPreview;
