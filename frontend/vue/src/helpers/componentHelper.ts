import {Vue} from "vue-property-decorator";

export function componentHasSlot(vueInstance: Vue, slotName: string): boolean {
    return !!vueInstance.$slots[slotName];
}
