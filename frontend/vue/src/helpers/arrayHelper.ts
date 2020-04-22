export function arrayRemoveObjectsDuplicates(arr: any[], key: string): any[] {
    let res = [];
    arr.forEach(item => {
        if (!res.some(itemIn => itemIn[key] === item[key])) {
            //@ts-ignore
            res.push(item);
        }
    });
    return res;
}
