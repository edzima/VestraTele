import {hideAll} from 'tippy.js';

export function ignoreAllSelections(): void {
    if (window.getSelection) {
        window.getSelection()?.removeAllRanges();
    }
}

export function hideAllTippy(): void {
    hideAll();
    setTimeout(() => {
        hideAll();
    }, 100);
    setTimeout(() => {
        hideAll();
    }, 500);
}
