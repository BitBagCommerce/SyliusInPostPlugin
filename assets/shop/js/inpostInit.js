import {GeoWidget} from '../../common/js/geowidget';
import {InpostPointEvents} from './inpostPointEvents';

if (document.querySelectorAll('[data-bb-target="inpost-geowidget"]').length > 0) {
    new GeoWidget().init();
    new InpostPointEvents().init();
}
