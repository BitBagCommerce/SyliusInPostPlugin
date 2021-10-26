const triggerCustomEvent = (node, eventName, data) => {
    const eventPrefix = "bb";
    const costomEvent = new Event(`${eventPrefix}.${eventName}`, data)

    node.dispatchEvent(costomEvent)

    return node;
}

export default triggerCustomEvent
