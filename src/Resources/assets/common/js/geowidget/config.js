const API_POINTS = 'https://api-pl-points.easypack24.net/v1/points';

const DEFAULT_SELECTORS = {
    button: '[data-bb-event="select-inpost-point"]',
    wrapper: '[data-bb-event="preview-inpost-point"]',
    preview: '[data-bb-inpost-preview]',
    previewRaw: 'data-bb-inpost-preview',
    testPointPatter: 'data-test-point-name="%point_name%"'
};

const DEFAULT_EASYPACK_CONFIG = {
    defaultLocale: 'pl',
    mapType: 'osm',
    searchType: 'osm',
    points: {
        types: ['parcel_locker'],
    },
    map: {
        initialTypes: ['parcel_locker'],
    },
};

export {API_POINTS, DEFAULT_SELECTORS, DEFAULT_EASYPACK_CONFIG};
