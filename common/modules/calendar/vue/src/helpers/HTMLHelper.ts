export function createBadge(background: string, text: string, color?: string): HTMLElement {
    const element = document.createElement('span');
    element.classList.add('event-badge');
    element.textContent = text;
    element.style.backgroundColor = background;
    if(color && color.length){
        element.style.color = color;
    }

    return element;
}
