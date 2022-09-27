export function createBadge(color: string, text: string): HTMLElement {
    const element = document.createElement('span');
    element.classList.add('event-badge');
    element.textContent = text;
    element.style.backgroundColor = color;
    return element;
}
