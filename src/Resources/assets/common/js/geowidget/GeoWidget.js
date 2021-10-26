import { GeoWidgetButton, GeoWidgetPreview } from "./index";
import { DEFAULT_SELECTORS, DEFAULT_EASYPACK_CONFIG } from "./config";

export class GeoWidget {
    constructor(options = {}) {
        const { selectors, easyPackConfig } = options;

        this.selectors = Object.assign({}, DEFAULT_SELECTORS, selectors);
        this.easyPackConfig = Object.assign({}, DEFAULT_EASYPACK_CONFIG, easyPackConfig);
    }

    init() {
        window.easyPackAsyncInit = () => easyPack.init(this.easyPackConfig);

        this.initButtons();
        this.initPreviews();
        return this;
    }

    initButtons() {
        const nodes = [...document.querySelectorAll(this.selectors.button)]
        const buttons = nodes.map(node => new GeoWidgetButton(node))
        return buttons
    }

    initPreviews() {
        const nodes = [...document.querySelectorAll(this.selectors.wrapper)]
        const wrappers = nodes.map(node => new GeoWidgetPreview(node).renderFromCode(node.dataset.bbPoint))
        return wrappers
    }
}

export default GeoWidget;
