//@todo add phone validator
export function telLink(number: string, text?: string): HTMLElement {
    const element = document.createElement('a');
    element.href = 'tel:' + number;
    if (!text) {
        text = number;
    }
    element.text = text;
    element.classList.add('tel-link');
    return element;
}
