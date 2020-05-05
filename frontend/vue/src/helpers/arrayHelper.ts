export function removeDuplicateObjects(arr: any[], key: string): any[] {
    const seen: any[] = [];

    return arr.filter((item: any) => {
        const keyVal: any = item[key];
        if (seen.includes(keyVal)) {
            return false
        } else {
            seen.push(keyVal);
            return true
        }
    })

}
