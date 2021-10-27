import {GeoWidget} from '../../common/js/geowidget';

new GeoWidget().init();

class InpostPointEvents {
    constructor() {
        const inputs = [...document.querySelectorAll('[value="inpost_point"]')];
        this.shippingGroups = inputs.map((input) => [...document.querySelectorAll(`[name="${input.name}"]`)]);
        this.watchInputChanges();
    }

    watchInputChanges() {
        this.shippingGroups.forEach((groupFields) => {
            groupFields.forEach((field) => {
                field.addEventListener('change', this.addCustomEvents);
            });
        });
    }

    addCustomEvents({currentTarget}) {
        const IsInpostPointSelected = currentTarget.value === 'inpost_point';
        const event = new Event(`bb.inpost.point.${IsInpostPointSelected ? 'selected' : 'deselected'}`);
        currentTarget.dispatchEvent(event);
    }
}

new InpostPointEvents();
